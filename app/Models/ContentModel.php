<?php
namespace App\Models;
use CodeIgniter\Model;
use Config\Database;

class ContentModel extends Model {
    protected $db;

    public function __construct() {
        parent::__construct();
        $this->db = Database::connect();
    }

/**
 * Get all media types ordered by position.
 *
 * @return list<array<string, mixed>>
 */
public function getMediaTypes(): array {
    return $this->db->table('media_types')
        ->orderBy('position', 'ASC')
        ->get()
        ->getResultArray();
}

/**
 * Get paginated media rows for a specific media type.
 *
 * @param int $mediaTypeId Media type identifier.
 * @param int $offset Pagination offset.
 * @param int $limit Number of rows to return.
 *
 * @return list<array<string, mixed>>
 */
public function getMedia(int $mediaTypeId, int $offset = 0, int $limit = 50): array {
    return $this->db->table('media')
        ->where('media_type_id', $mediaTypeId)
        ->orderBy('creator', 'ASC')
        ->orderBy('title', 'ASC')
        ->orderBy('collection', 'ASC')
        ->limit($limit, $offset)
        ->get()
        ->getResultArray();
}

/**
 * Translate a media type field value to another field value.
 *
 * Example: translateMediaType('alias', 'id', $alias).
 *
 * @param string $from Source column name.
 * @param string $to Target column name.
 * @param string $value Source value to translate.
 *
 * @return string|null Translated value, or null when no match exists.
 */
public function translateMediaType(string $from, string $to, string $value): ?string {
    $row = $this->db->table('media_types')
        ->select($to)
        ->where($from, $value)
        ->get()
        ->getRowArray();

    return isset($row[$to]) ? (string) $row[$to] : null;
}

/**
 * Count all media rows for a specific media type.
 *
 * @param int $mediaTypeId Media type identifier.
 *
 * @return int
 */
public function getMediaCount(int $mediaTypeId): int {
    return (int) $this->db->table('media')
        ->where('media_type_id', $mediaTypeId)
        ->countAllResults();
}

/**
 * Get the most popular creators for a media type.
 *
 * Excludes placeholder creators named "---".
 *
 * @param int $type Media type identifier.
 * @param int $limit Maximum number of creators to return.
 *
 * @return list<array<string, mixed>>
 */
public function mostPopular(int $type = 1, int $limit = 17): array {
    $limit = max(1, $limit);

    return $this->db->table('media')
        ->select('creator, COUNT(*) as count')
        ->where('media_type_id', $type)
        ->where('creator !=', '---')
        ->groupBy('creator')
        ->orderBy('count', 'DESC')
        ->limit($limit)
        ->get()
        ->getResultArray();
}

} // ─── End of Class ───
