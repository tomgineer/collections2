<?php
namespace App\Models;
use CodeIgniter\Database\ConnectionInterface;

class Import {
    protected $db;
    public function __construct(ConnectionInterface $db) {
        $this->db = $db;
    }


} // ─── End of Class ───
