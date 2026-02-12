<?php
namespace App\Models;
use CodeIgniter\Model;
use Config\Database;

class ImportModel extends Model {

/**
 * Runs the import pipeline when exported Obsidian HTML files have changed.
 *
 * If changes are detected, this imports files into `media`, refreshes media
 * type stats, and removes runtime artifacts.
 */
public function initImport(): void {
    if ( $this->checkObsidianHtml() ) {
        $this->importFilesToDatabase();
        $this->calcMediaTypeStats();
        $this->clearRunTimeArtifacts();
        $this->updateLastUpdatedMetric();
    }
}
/**
 * Checks the obsidian_html export directory for HTML file timestamp changes.
 *
 * @return bool True when the current file state differs from the stored manifest.
 */
private function checkObsidianHtml(): bool {
    $directory = WRITEPATH . 'obsidian_html';
    $manifestPath = $directory . DIRECTORY_SEPARATOR . 'manifest.json';

    if (!is_dir($directory)) {
        return false;
    }

    $current = [];
    $entries = @scandir($directory);
    if ($entries === false) {
        return false;
    }

    foreach ($entries as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }

        $path = $directory . DIRECTORY_SEPARATOR . $entry;
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

        $current[$entry] = (int) $mtime;
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
 * Imports Obsidian-exported collection HTML tables into the `media` table.
 *
 * Expected filenames are fixed and mapped to `media_type_id`:
 * - cds-collection.html => 1
 * - books-collection.html => 2
 * - arkas-collection.html => 3
 * - blu-ray-collection.html => 4
 *
 * For each file, the first table is parsed and row values are read by fixed
 * column order, then normalized and inserted. Existing `media` rows are
 * replaced in a transaction (`truncate` + `insertBatch`).
 */
private function importFilesToDatabase(): void {
    $directory = WRITEPATH . 'obsidian_html';
    if (!is_dir($directory)) {
        return;
    }

    $fileToTypeId = [
        'cds-collection.html' => 1,
        'books-collection.html' => 2,
        'arkas-collection.html' => 3,
        'blu-ray-collection.html' => 4,
    ];

    $rowsToInsert = [];

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

            $title = '';
            $creator = '';
            $collection = '';

            if ($fileName === 'cds-collection.html') {
                // Artist | Title
                $creator = $cells[0] ?? '';
                $title = $cells[1] ?? '';
            } elseif ($fileName === 'books-collection.html') {
                // Title | Author
                $title = $cells[0] ?? '';
                $creator = $cells[1] ?? '';
            } elseif ($fileName === 'arkas-collection.html') {
                // Title | Series
                $title = $cells[0] ?? '';
                $collection = $cells[1] ?? '';
            } elseif ($fileName === 'blu-ray-collection.html') {
                // Title
                $title = $cells[0] ?? '';
            }

            if ($title === '') {
                continue;
            }

            $rowsToInsert[] = [
                'media_type_id' => $mediaTypeId,
                'title' => $title,
                'creator' => $creator,
                'collection' => $collection,
                'search_query' => $this->buildSearchQuery($mediaTypeId, $title, $creator, $collection),
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
 * Build a full Google search URL for an item and store it in `media.search_query`.
 */
private function buildSearchQuery(int $mediaTypeId, string $title, string $creator = '', string $collection = ''): string {
    $query = '';

    if ($mediaTypeId === 1) { // cds
        $searchCreator = ($creator === '---') ? 'Various Artists' : $creator;
        $query = trim($searchCreator . ' ' . $title . ' CD tracklist');
    } elseif ($mediaTypeId === 2) { // books
        $query = trim($creator . ' ' . $title . ' book');
    } elseif ($mediaTypeId === 3) { // arkas
        $query = trim('Αρκάς ' . $title);
    } elseif ($mediaTypeId === 4) { // blu-rays
        $query = trim($title . ' movie');
    } else {
        $query = trim($title . ' ' . $creator . ' ' . $collection);
    }

    return 'https://www.google.com/search?' . http_build_query(['q' => $query]);
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
 * Recalculate and persist per-media-type item counts and MSRP totals.
 */
private function calcMediaTypeStats(): void {
    $sql = <<<SQL
UPDATE media_types mt
LEFT JOIN (
    SELECT media_type_id, COUNT(*) AS items_count
    FROM media
    GROUP BY media_type_id
) m ON m.media_type_id = mt.id
SET
    mt.items_count = COALESCE(m.items_count, 0),
    mt.total_msrp = COALESCE(m.items_count, 0) * mt.item_msrp
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
