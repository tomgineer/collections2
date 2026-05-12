<?php
namespace App\Models;
use CodeIgniter\Model;
use Config\Database;

class ContentModel extends Model {

/**
 * Get all media categories ordered by position.
 *
 * @return list<array<string, mixed>>
 */
public function getMediaTypes(): array {
    return $this->db->table('media_categories')
        ->orderBy('position', 'ASC')
        ->get()
        ->getResultArray();
}

/**
 * Get paginated media rows for a specific media category.
 *
 * @param int $mediaTypeId Media category identifier.
 * @param int $offset Pagination offset.
 * @param int $limit Number of rows to return.
 *
 * @return list<array<string, mixed>>
 */
public function getMedia(int $mediaTypeId, int $offset = 0, int $limit = 50): array {
    return $this->db->table('media')
        ->where('media_category_id', $mediaTypeId)
        ->orderBy('creator', 'ASC')
        ->orderBy('title', 'ASC')
        ->limit($limit, $offset)
        ->get()
        ->getResultArray();
}

/**
 * Translate a media category field value to another field value.
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
    $row = $this->db->table('media_categories')
        ->select($to)
        ->where($from, $value)
        ->get()
        ->getRowArray();

    return isset($row[$to]) ? (string) $row[$to] : null;
}

/**
 * Count all media rows for a specific media category.
 *
 * @param int $mediaTypeId Media category identifier.
 *
 * @return int
 */
public function getMediaCount(int $mediaTypeId): int {
    return (int) $this->db->table('media')
        ->where('media_category_id', $mediaTypeId)
        ->countAllResults();
}

/**
 * Get the most popular creators for a media category.
 *
 * Excludes placeholder creators named "---".
 *
 * @param int $type Media category identifier.
 * @param int $limit Maximum number of creators to return.
 *
 * @return list<array<string, mixed>>
 */
public function mostPopular(int $type = 1, int $limit = 17): array {
    $limit = max(1, $limit);

    return $this->db->table('media')
        ->select('creator, COUNT(*) as count')
        ->where('media_category_id', $type)
        ->where('creator !=', '---')
        ->groupBy('creator')
        ->orderBy('count', 'DESC')
        ->limit($limit)
        ->get()
        ->getResultArray();
}

/**
 * Get all metrics rows.
 *
 * @return list<array<string, mixed>>
 */
public function getMetrics(): array {
    return $this->db->table('metrics')
        ->orderBy('id', 'ASC')
        ->get()
        ->getResultArray();
}

} // ─── End of Class ───
