<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;

class TrackVisitor implements FilterInterface
{
    /**
     * Increment `metrics.metric_value` where `metric_key` is `hits`.
     */
    public function before(RequestInterface $request, $arguments = null) {
        try {
            $db = Database::connect();

            $db->query(
                "UPDATE metrics
                 SET metric_value = CAST(COALESCE(NULLIF(metric_value, ''), '0') AS UNSIGNED) + 1
                 WHERE metric_key = 'hits'"
            );

            if ($db->affectedRows() === 0) {
                $db->table('metrics')->insert([
                    'metric_key' => 'hits',
                    'metric_value' => '1',
                ]);
            }
        } catch (\Throwable $e) {
            // Do not break the request if tracking fails.
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
