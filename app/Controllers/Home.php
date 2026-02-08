<?php
namespace App\Controllers;

use App\Models\ContentModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Home extends BaseController{
    public function index(): string {
        (new \App\Models\ImportModel())->initImport();

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
        $mediaTypeId = $contentModel->translateMediaType('alias', 'id', $alias);
        if ($mediaTypeId === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $perPage = 100;
        $page = max(1, (int) $this->request->getGet('page'));
        $totalItems = $contentModel->getMediaCount($mediaTypeId);
        $totalPages = max(1, (int) ceil($totalItems / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $media = $contentModel->getMedia($mediaTypeId, $offset, $perPage);
        $data = [
            'media' => $media,
            'alias' => $alias,
            'label' => $contentModel->translateMediaType('alias', 'media_type', $alias) ?? 'Media',
            'page' => $page,
            'perPage' => $perPage,
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
        ];
        return view('media', $data);
    }
}
