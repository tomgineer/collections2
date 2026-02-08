<?php
namespace App\Controllers;

use App\Models\ContentModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Home extends BaseController{
    public function index(): string {

        $data = [
            'status' => 0
        ];

        return view('home', $data);
    }

    public function about(): string {
        (new \App\Models\ImportModel())->initImport();
        $data = [
            'status' => 0
        ];
        return view('about', $data);
    }

    public function media(string $alias): string {
        $alias = strtolower(trim($alias));
        if ($alias === '' || ! preg_match('/^[a-z0-9-]+$/', $alias)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $contentModel = new ContentModel();
        $mediaTypeId = $contentModel->getMediaTypeIdByAlias($alias);
        if ($mediaTypeId === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $media = $contentModel->getMedia($mediaTypeId);
        $data = [
            'media' => $media,
            'alias' => $alias,
            'label' => $contentModel->getMediaTypeLabelByAlias($alias)
        ];
        return view('media', $data);
    }
}
