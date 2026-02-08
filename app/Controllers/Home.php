<?php
namespace App\Controllers;
use App\Models\ImportModel;

class Home extends BaseController{
    public function index(): string {
        $status = (new ImportModel())->checkObsidianHtml();
        $data = [
            'status' => $status
        ];

        return view('home', $data);
    }
}
