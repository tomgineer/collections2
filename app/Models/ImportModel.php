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

/**
 * Checks the obsidian_html export directory for HTML file timestamp changes.
 *
 * @return bool True when the current file state differs from the stored manifest.
 */
public function checkObsidianHtml(): bool {
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


} // ─── End of Class ───