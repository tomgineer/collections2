<?php
namespace App\Models;
use CodeIgniter\Model;
use Config\Database;

class ImportModel extends Model {
    protected $db;

    public function __construct() {
        parent::__construct();
        $this->db = Database::connect();
    }

public function initImport(): void {
    if ( $this->checkObsidianHtml() ) {
        $this->importFilesToDatabase();
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
            ];
        }
    }

    if (empty($rowsToInsert)) {
        return;
    }

    $this->db->transStart();
    $this->db->table('media')->truncate();
    $this->db->table('media')->insertBatch($rowsToInsert);
    $this->db->transComplete();
}

} // ─── End of Class ───
