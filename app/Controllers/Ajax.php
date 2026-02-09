<?php
namespace App\Controllers;
use CodeIgniter\API\ResponseTrait;
use App\Models\AjaxModel;

class Ajax extends BaseController{
use ResponseTrait;

public function search() {
    // Ensure it's an AJAX request
    if (! $this->request->isAJAX()) {
        return $this->failForbidden('Not an AJAX request');
    }

    // Get search term from query string: /ajax/search?q=foo
    $query = trim((string) $this->request->getGet('q'));

    // Gracefully ignore short or empty queries
    if ($query === '' || mb_strlen($query) < 2) {
        return $this->respond([]);
    }

    try {
        $results = (new AjaxModel())->search($query);
        if (! is_array($results)) {
            $results = [];
        }

        return $this->respond($results);
    } catch (\Throwable $e) {
        log_message('error', 'Search error: {message}', ['message' => $e->getMessage()]);
        return $this->failServerError('An error occurred while performing the search.');
    }
}

} // ─── End of Class ───
