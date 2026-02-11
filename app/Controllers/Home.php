<?php
namespace App\Controllers;

use App\Models\ContentModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Home extends BaseController{
/**
 * Render the homepage.
 *
 * @return string
 */
public function index(): string {
    $cacheName = 'page-home';
    if ($cachedView = cache($cacheName)) {
        return $cachedView;
    }

    $contentModel = new ContentModel();
    $data = ['mostPopular' => $contentModel->mostPopular()];
    $output = view('home', $data);
    cache()->save($cacheName, $output, 86400);

    return $output;
}

/**
 * Render the about page.
 *
 * @return string
 */
public function about(): string {
    (new \App\Models\ImportModel())->initImport();
    return view('about');
}

/**
 * Render a media listing page by media type alias.
 *
 * @param string $alias Media type alias from the route.
 *
 * @throws PageNotFoundException
 *
 * @return string
 */
public function media(string $alias): string {
    $alias = strtolower(trim($alias));
    if ($alias === '' || ! preg_match('/^[a-z0-9-]+$/', $alias)) {
        throw PageNotFoundException::forPageNotFound();
    }

    $page = max(1, (int) $this->request->getGet('page'));

    // Try serving the cached view
    $cacheName = "page-media-{$alias}-p{$page}";
    if ($cachedView = cache($cacheName)) {
        return $cachedView;
    }

    $contentModel = new ContentModel();
    $mediaTypeId = $contentModel->translateMediaType('alias', 'id', $alias);
    if ($mediaTypeId === null) {
        throw PageNotFoundException::forPageNotFound();
    }

    $perPage = 100;
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

    $output = view('media', $data);
    cache()->save($cacheName, $output, 86400);

    return $output;
}

} // ─── End of Class ───
