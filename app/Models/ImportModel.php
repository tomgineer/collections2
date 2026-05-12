<?php
namespace App\Models;
use CodeIgniter\Model;
use Config\Database;

class ImportModel extends Model {

/**
 * Runs the import pipeline when exported Obsidian HTML files have changed.
 *
 * If changes are detected, this imports files into `media`, refreshes media
 * category stats, and removes runtime artifacts.
 */
public function initImport(): void {
    if ($this->checkImportSources()) {
        $this->importFilesToDatabase();
        $this->calcMediaTypeStats();
        $this->clearRunTimeArtifacts();
        $this->updateLastUpdatedMetric();
    }
}
/**
 * Checks import source files for timestamp changes.
 *
 * @return bool True when the current file state differs from the stored manifest.
 */
private function checkImportSources(): bool {
    $htmlDirectory = WRITEPATH . 'obsidian_html';
    $jsonDirectory = WRITEPATH . 'json';
    $manifestPath = WRITEPATH . 'import-manifest.json';
    $current = [];

    if (is_dir($htmlDirectory)) {
        $entries = @scandir($htmlDirectory);
        if ($entries !== false) {
            foreach ($entries as $entry) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }

                $path = $htmlDirectory . DIRECTORY_SEPARATOR . $entry;
                if (!is_file($path)) {
                    continue;
                }

                if (strtolower(pathinfo($entry, PATHINFO_EXTENSION)) !== 'html') {
                    continue;
                }

                $mtime = @filemtime($path);
                if ($mtime === false) {
                    continue;
                }

                $current['obsidian_html/' . $entry] = (int) $mtime;
            }
        }
    }

    foreach (['music.json', 'movies.json', 'shows.json'] as $jsonFile) {
        $jsonPath = $jsonDirectory . DIRECTORY_SEPARATOR . $jsonFile;
        if (!is_file($jsonPath)) {
            continue;
        }

        $mtime = @filemtime($jsonPath);
        if ($mtime !== false) {
            $current['json/' . $jsonFile] = (int) $mtime;
        }
    }

    if ($current === []) {
        return false;
    }

    ksort($current);

    $previous = null;
    if (is_file($manifestPath)) {
        $raw = @file_get_contents($manifestPath);
        if ($raw !== false) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                if (isset($decoded['files']) && is_array($decoded['files'])) {
                    $previous = $decoded['files'];
                } else {
                    $previous = $decoded;
                }
            }
        }
    }

    $changed = !is_array($previous) || $previous !== $current;

    if ($changed) {
        $manifestData = [
            'updated_at' => time(),
            'files' => $current,
        ];

        @file_put_contents(
            $manifestPath,
            json_encode($manifestData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
    }

    return $changed;
}

/**
 * Imports collection source files into the `media` table.
 *
 * Expected sources are fixed and mapped to `media_category_id`:
 * - writable/json/music.json => 1
 * - books-collection.html => 2
 * - arkas-collection.html => 3
 * - writable/json/movies.json => 4
 * - writable/json/shows.json => 5
 *
 * HTML-backed media types still parse the first table by fixed column order,
 * then normalize and insert rows. Existing `media` rows are replaced in a
 * transaction (`truncate` + `insertBatch`).
 */
private function importFilesToDatabase(): void {
    $directory = WRITEPATH . 'obsidian_html';
    $fileToTypeId = [
        'books-collection.html' => 2,
        'arkas-collection.html' => 3,
    ];

    $rowsToInsert = [];
    $this->importMusicJson($rowsToInsert);
    $this->importMoviesJson($rowsToInsert);
    $this->importShowsJson($rowsToInsert);

    if (!is_dir($directory) && $rowsToInsert === []) {
        return;
    }

    foreach ($fileToTypeId as $fileName => $mediaTypeId) {
        $rawPath = $directory . DIRECTORY_SEPARATOR . $fileName;
        if (!is_file($rawPath)) {
            continue;
        }

        $html = @file_get_contents($rawPath);
        if ($html === false) {
            continue;
        }

        $dom = new \DOMDocument();
        $prevUseInternal = libxml_use_internal_errors(true);
        $loaded = $dom->loadHTML($html);
        libxml_clear_errors();
        libxml_use_internal_errors($prevUseInternal);

        if ($loaded === false) {
            continue;
        }

        $tables = $dom->getElementsByTagName('table');
        $table = $tables->item(0);
        if (!$table) {
            continue;
        }

        $tbodyList = $table->getElementsByTagName('tbody');
        $sourceNode = $tbodyList->length > 0 ? $tbodyList->item(0) : $table;

        foreach ($sourceNode->getElementsByTagName('tr') as $tr) {
            $cells = [];
            foreach ($tr->getElementsByTagName('td') as $td) {
                $value = trim(preg_replace('/\s+/u', ' ', $td->textContent ?? ''));
                $cells[] = $value;
            }

            if (empty($cells)) {
                continue;
            }

            // HTML imports currently use the same first-two-column mapping.
            $title = $cells[0] ?? '';
            $creator = $cells[1] ?? '';

            if ($title === '') {
                continue;
            }

            $format = in_array($fileName, ['books-collection.html', 'arkas-collection.html'], true) ? 'Paperback' : '';

            $rowsToInsert[] = [
                'media_category_id' => $mediaTypeId,
                'title' => $title,
                'creator' => $creator,
                'format' => $format,
                'updated' => date('Y-m-d H:i:s'),
            ];
        }
    }

    if (empty($rowsToInsert)) {
        return;
    }

    $this->db->transStart();
    $this->db->table('media')->truncate();
    $this->db->table('media')->insertBatch($rowsToInsert);
    $this->calcMediaTypeStats();
    $this->db->transComplete();
}

/**
 * Imports Music entries from writable/json/music.json into the `media` rows buffer.
 *
 * Expected JSON row shape:
 * - artist => creator
 * - album => title
 * - format => optional filter, only `CD` rows are imported when present
 *
 * @param array<int, array<string, mixed>> $rowsToInsert
 */
private function importMusicJson(array &$rowsToInsert): void {
    $jsonPath = WRITEPATH . 'json' . DIRECTORY_SEPARATOR . 'music.json';
    if (!is_file($jsonPath)) {
        return;
    }

    $raw = @file_get_contents($jsonPath);
    if ($raw === false) {
        return;
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return;
    }

    foreach ($decoded as $row) {
        if (!is_array($row)) {
            continue;
        }

        $format = trim((string) ($row['format'] ?? ''));
        if ($format !== '' && strcasecmp($format, 'CD') !== 0) {
            continue;
        }

        $creator = trim((string) ($row['artist'] ?? ''));
        $title = trim((string) ($row['album'] ?? ''));

        if ($title === '') {
            continue;
        }

        $rowsToInsert[] = [
            'media_category_id' => 1,
            'title' => $title,
            'creator' => $creator,
            'format' => 'CD',
            'updated' => date('Y-m-d H:i:s'),
        ];
    }
}

/**
 * Imports Movies from `movies.json` into the `media` rows buffer.
 *
 * Expected JSON row shape:
 * - movie => title
 *
 * @param array<int, array<string, mixed>> $rowsToInsert
 */
private function importMoviesJson(array &$rowsToInsert): void {
    $jsonDirectory = WRITEPATH . 'json';

    $moviePath = $jsonDirectory . DIRECTORY_SEPARATOR . 'movies.json';
    $movieRows = $this->decodeJsonFile($moviePath);
    foreach ($movieRows as $row) {
        if (!is_array($row)) {
            continue;
        }

        $format = $this->resolveVideoFormat($row['format'] ?? '');
        if ($format === '') {
            continue;
        }

        $title = trim((string) ($row['movie'] ?? ''));
        if ($title === '') {
            continue;
        }

        $rowsToInsert[] = [
            'media_category_id' => 4,
            'title' => $title,
            'creator' => '',
            'format' => $format,
            'updated' => date('Y-m-d H:i:s'),
        ];
    }
}

/**
 * Imports Shows from `shows.json` into the `media` rows buffer.
 *
 * Expected JSON row shape:
 * - show + season => "Show - Season XX"
 *
 * @param array<int, array<string, mixed>> $rowsToInsert
 */
private function importShowsJson(array &$rowsToInsert): void {
    $jsonDirectory = WRITEPATH . 'json';

    $showPath = $jsonDirectory . DIRECTORY_SEPARATOR . 'shows.json';
    $showRows = $this->decodeJsonFile($showPath);
    foreach ($showRows as $row) {
        if (!is_array($row)) {
            continue;
        }

        $format = $this->resolveVideoFormat($row['format'] ?? '');
        if ($format === '') {
            continue;
        }

        $show = trim((string) ($row['show'] ?? ''));
        $season = trim((string) ($row['season'] ?? ''));
        $title = trim($show . ' - ' . $season, ' -');
        if ($title === '') {
            continue;
        }

        $rowsToInsert[] = [
            'media_category_id' => 5,
            'title' => $title,
            'creator' => '',
            'format' => $format,
            'updated' => date('Y-m-d H:i:s'),
        ];
    }
}

/**
 * Normalize a movie/show format value to the stored media format.
 */
private function resolveVideoFormat(mixed $value): string {
    $format = trim((string) $value);
    if ($format === '') {
        return 'Blu-Ray';
    }

    if (strcasecmp($format, 'Blu-Ray') === 0) {
        return 'Blu-Ray';
    }

    if (strcasecmp($format, 'DVD') === 0) {
        return 'DVD';
    }

    return '';
}

/**
 * Decode a JSON file into an array payload or return an empty array on failure.
 *
 * @return array<int|string, mixed>
 */
private function decodeJsonFile(string $path): array {
    if (!is_file($path)) {
        return [];
    }

    $raw = @file_get_contents($path);
    if ($raw === false) {
        return [];
    }

    $decoded = json_decode($raw, true);

    return is_array($decoded) ? $decoded : [];
}

/**
 * Persist the last successful import completion timestamp in `metrics`.
 */
private function updateLastUpdatedMetric(): void {
    $timestamp = date('Y-m-d H:i:s');

    $this->db->table('metrics')
        ->where('metric_key', 'last_updated')
        ->set('metric_value', $timestamp)
        ->update();
}

/**
 * Recalculate and persist per-category item counts and MSRP totals.
 */
private function calcMediaTypeStats(): void {
    $sql = <<<SQL
UPDATE media_categories mc
LEFT JOIN (
    SELECT media_category_id, COUNT(*) AS items_count
    FROM media
    GROUP BY media_category_id
) m ON m.media_category_id = mc.id
SET
    mc.items_count = COALESCE(m.items_count, 0),
    mc.total_msrp = COALESCE(m.items_count, 0) * mc.item_msrp
SQL;

    $this->db->query($sql);
}

/**
 * Clears runtime artifact files under WRITEPATH.
 *
 * Behavior:
 * - Attempts to clear framework cache via cache()->clean().
 * - Deletes all files (except `index.html`) from:
 *   `writable/session`, `writable/logs`, and `writable/cache`.
 *
 * Notes:
 * - This is an aggressive cleanup and may invalidate active sessions.
 * - Subdirectories are ignored; only top-level files are removed.
 */
public function clearRunTimeArtifacts(): void {
    try {
        cache()->clean();
    } catch (\Throwable $e) {}

    foreach (['session', 'logs'] as $dir) {
        $path = WRITEPATH . $dir . '/';
        if (! is_dir($path)) {
            continue;
        }

        foreach (new \FilesystemIterator($path) as $file) {
            if ($file->isFile() && $file->getFilename() !== 'index.html') {
                @unlink($file->getRealPath());
            }
        }
    }
}


} // ─── End of Class ───
