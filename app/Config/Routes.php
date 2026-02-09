<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

/**
 * --------------------------------------------------------------------
 * Custom Routes
 * --------------------------------------------------------------------
 */

//  (:any) -> will match all characters from that point to the end of the URI. This may include multiple URI segments.
//  (:segment) -> will match any character except for a forward slash (/) restricting the result to a single segment.
//  (:num) -> will match any integer.
//  (:alpha) -> will match any string of alphabetic characters
//  (:alphanum) -> will match any string of alphabetic characters or integers, or any combination of the two.
//  (:hash) -> is the same as (:segment), but can be used to easily see which routes use hashed ids.

$routes->get('media/(:segment)', 'Home::media/$1');

/**
 * Ajax
 */
$routes->get('ajax/search', 'Ajax::search');