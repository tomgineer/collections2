<?php
namespace App\Models;
use CodeIgniter\Model;
use Config\Database;

class AjaxModel extends Model {
    protected $db;

    public function __construct() {
        parent::__construct();
        $this->db = Database::connect();
    }

/**
 * Searches media records by creator or title using SQL LIKE matching.
 *
 * Performs a case-insensitive partial match on both the `creator` and `title`
 * columns using `%term%` (LIKE 'both'), allowing searches to match substrings
 * anywhere within the field (e.g. "U2", "R.E.M", partial titles).
 *
 * Queries shorter than 2 characters are ignored.
 *
 * @param string $term Search term entered by the user
 * @return array<int, array<string, mixed>> Matching media records (max 10)
 */
public function search(string $term): array {
    $term = trim($term);

    // Keep your original minimum length rule
    if ($term === '' || mb_strlen($term) < 2) {
        return [];
    }

    return $this->db->table('media m')
        ->select([
            'm.creator',
            'm.title',
            'm.collection',
            'mt.media_type AS type',
        ])
        ->join('media_types mt', 'mt.id = m.media_type_id', 'left')
        ->groupStart()
            ->like('m.creator', $term, 'both')
            ->orLike('m.title', $term, 'both')
        ->groupEnd()
        ->orderBy('m.creator', 'ASC')
        ->orderBy('m.title', 'ASC')
        ->limit(15)
        ->get()
        ->getResultArray();
}


} // ─── End of Class ───
