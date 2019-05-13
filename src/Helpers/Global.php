<?php

use Laradium\Laradium\Documents\Services\DocumentService;

if (!function_exists('documentColumns')) {

    /**
     * @param $blueprint
     */
    function documentColumns($blueprint)
    {
        DocumentService::columns($blueprint);
    }
}
