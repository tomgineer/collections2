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

public function getMediaTypes(): array {
    return $this->db->table('media_types')
        ->orderBy('position', 'ASC')
        ->get()
        ->getResultArray();
}

public function getMedia(int $mediaTypeId): array {
    return $this->db->table('media')
        ->where('media_type_id', $mediaTypeId)
        ->orderBy('creator', 'ASC')
        ->orderBy('title', 'ASC')
        ->orderBy('collection', 'ASC')
        ->get()
        ->getResultArray();
}

public function getMediaTypeIdByAlias(string $alias): ?int {
    $row = $this->db->table('media_types')
        ->select('id')
        ->where('alias', $alias)
        ->get()
        ->getRowArray();

    return isset($row['id']) ? (int) $row['id'] : null;
}

public function getMediaTypeLabelByAlias(string $alias): string {
    $row = $this->db->table('media_types')
        ->select('media_type')
        ->where('alias', $alias)
        ->get()
        ->getRowArray();

    return isset($row['media_type']) ? (string) $row['media_type'] : 'Media';
}

} // ─── End of Class ───
