<?php

defined('BASE_PATH') or define('BASE_PATH', __DIR__ . '/../../../');

/**
 * Helpers - Paths
 */

/**
 * Return `app` directory
 *
 * @param string $filePath
 * @return string
 */
function app_path($filePath=null)
{
    $relatedPath = DIRECTORY_SEPARATOR . "app";
    $relatedPath = ($filePath) ? $relatedPath . DIRECTORY_SEPARATOR . trim($filePath, DIRECTORY_SEPARATOR) : $relatedPath;

    return base_path($relatedPath);
}

/**
 * Return project root directory
 *
 * @param string $filePath
 * @return string
 */
function base_path($filePath=nul)
{
    $path = BASE_PATH;

    return $path = ($filePath) ? $path . DIRECTORY_SEPARATOR . trim($filePath, DIRECTORY_SEPARATOR) : $path;
}

/**
 * Return `public` directory
 *
 * @param string $filePath
 * @return string
 */
function public_path($filePath=nul)
{
    $relatedPath = DIRECTORY_SEPARATOR . "public";
    $relatedPath = ($filePath) ? $relatedPath . DIRECTORY_SEPARATOR . trim($filePath, DIRECTORY_SEPARATOR) : $relatedPath;

    return base_path($relatedPath);
}

/**
 * Return `storage` directory
 *
 * @param string $filePath
 * @return string
 */
function storage_path($filePath=nul)
{
    $relatedPath = DIRECTORY_SEPARATOR . "storage";
    $relatedPath = ($filePath) ? $relatedPath . DIRECTORY_SEPARATOR . trim($filePath, DIRECTORY_SEPARATOR) : $relatedPath;

    return base_path($relatedPath);
}
