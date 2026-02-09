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
 * Search media records by creator/title with short-term and full-text strategies.
 *
 * @param string $term Raw search term from the request.
 * @return array<int, array<string, mixed>> Matching rows for the AJAX response.
 */
public function search(string $term): array {
    $term = trim($term);
    if ($term === '' || mb_strlen($term) < 2) {
        return [];
    }

    $builder = $this->db->table('media m')
        ->select(['m.creator','m.title','m.collection','mt.media_type AS type'])
        ->join('media_types mt', 'mt.id = m.media_type_id', 'left')
        ->limit(10);

    if (mb_strlen($term) < 3) {
        // short tokens like "U2" won't be in FULLTEXT
        $builder->groupStart()
            ->where('m.creator', $term)
            ->orLike('m.title', $term, 'both')
            ->groupEnd()
            ->orderBy('m.creator', 'ASC')
            ->orderBy('m.title', 'ASC');

        return $builder->get()->getResultArray();
    }

    $escaped = $this->db->escape($term . '*');

    $builder->select("MATCH(m.creator, m.title) AGAINST ($escaped IN BOOLEAN MODE) AS relevance", false)
        ->where("MATCH(m.creator, m.title) AGAINST ($escaped IN BOOLEAN MODE) > 0", null, false)
        ->orderBy('relevance', 'DESC')
        ->orderBy('m.creator', 'ASC')
        ->orderBy('m.title', 'ASC');

    return $builder->get()->getResultArray();
}

} // ─── End of Class ───
