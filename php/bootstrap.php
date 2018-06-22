<?php

/*
 * (c) 2018 by Georg Großberger <contact@grossberger-ge.org>
 *
 * This is free software; you can redistribute it and/or
 * modify it under the terms of the Apache License 2.0
 *
 * For the full copyright and license information see
 * <https://www.apache.org/licenses/LICENSE-2.0>
 */

require_once 'vendor/autoload.php';

$vars = [];
$fluid = new \TYPO3Fluid\Fluid\View\TemplateView();
$paths = $fluid->getTemplatePaths();

$args = isset($_SERVER['argv']) ? $_SERVER['argv'] : $argv;
array_shift($args);
$out = 'php://stdout';
$paths->setTemplatePathAndFilename('php://stdin');

while (count($args) > 0) {
    $arg = ltrim(array_shift($args), '-');
    $value = null;

    if (strpos($arg, '=') !== false) {
        list ($arg, $value) = explode('=', $arg, 2);
        $arg = trim($arg);
    }

    switch ($arg) {
        case 'o':
        case 'output':
            if (is_null($value)) {
                $value = array_shift($args);
            }

            if (trim($value) !== '') {
                $out = $value;
            }
            break;

        case 't':
        case 'templatesPath':
        case 'templatePath':
            if (is_null($value)) {
                $value = array_shift($args);
            }

            $paths->setTemplateRootPaths($paths->getTemplateRootPaths() + [$value]);
            break;

        case 'p':
        case 'partialsPath':
        case 'partialPath':
            if (is_null($value)) {
                $value = array_shift($args);
            }

            $paths->setPartialRootPaths($paths->getPartialRootPaths() + [$value]);
            break;

        case 'l':
        case 'layoutPath':
        case 'layoutsPath':
            if (is_null($value)) {
                $value = array_shift($args);
            }

            $paths->setLayoutRootPaths($paths->getLayoutRootPaths() + [$value]);
            break;

        case 's':
        case 'template':
        case 'source':
        case 'templateSource':
            if (is_null($value)) {
                $value = array_shift($args);
            }
            $paths->setTemplatePathAndFilename($value);
            break;

        case 'v':
        case 'variable':
        case 'variables':
            if (is_null($value)) {
                $value = array_shift($args);
            }

            $processed = @json_decode($value);

            if (json_last_error() === JSON_ERROR_NONE) {
                $fluid->assignMultiple($value);
            } else {
                $parts = explode('::', $value);

                if (count($parts) < 2) {
                    $fluid->assign($value, true);
                } else {
                    $fluid->assign($parts[0], $parts[1]);
                }
            }

            break;
    }
}

$result = $fluid->render();
file_put_contents($out, $result);
