<?php

defined('BASE_PATH') or define('BASE_PATH', __DIR__ . '/../../../');

/**
 * Helpers - Paths
 */

/**
 * return `app` directory
 *
 * @return string
 */
function app_path()
{
    return BASE_PATH . DIRECTORY_SEPARATOR . "app";
}

/**
 * return project root directory
 *
 * @return string
 */
function base_path()
{
    return BASE_PATH;
}

/**
 * return `public` directory
 *
 * @return string
 */
function public_path()
{
    return BASE_PATH . DIRECTORY_SEPARATOR . "public";
}

/**
 * return `storage` directory
 *
 * @param string $filepath
 * @return string
 */
function storage_path($filepath=null)
{
    $path = BASE_PATH . DIRECTORY_SEPARATOR . "storage";

    return $path = ($filepath) ? $path . DIRECTORY_SEPARATOR . $filepath : $path;
}
