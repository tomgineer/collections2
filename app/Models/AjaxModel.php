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

} // ─── End of Class ───
