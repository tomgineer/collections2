<?php

// Version
if(!function_exists('ver')) {
	function ver() {
        $version = defined('COLLECTIONS_VERSION') ? COLLECTIONS_VERSION : 'dev';
        return $version;
    }
}