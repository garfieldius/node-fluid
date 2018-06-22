#!/usr/bin/env php
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

if (is_file('fluid.phar')) {
    unlink('fluid.phar');
}

$phar = new Phar('fluid.phar');
$phar->addFile(__DIR__ . '/bootstrap.php', 'bootstrap.php');

$files = new RegexIterator(new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(
        __DIR__ . '/vendor',
        RecursiveDirectoryIterator::SKIP_DOTS | RecursiveDirectoryIterator::UNIX_PATHS | RecursiveDirectoryIterator::CURRENT_AS_FILEINFO
    ),
    RecursiveIteratorIterator::CHILD_FIRST | RecursiveIteratorIterator::LEAVES_ONLY
), '/\\.php$/');

$skip = strlen(realpath(__DIR__) . DIRECTORY_SEPARATOR);

foreach ($files as $file) {
    $name = realpath((string) $file);

    if (!preg_match('#/bin/|/doc/|/tests/|/examples/#', $name)) {
        $str = file_get_contents($name);
        $localName = substr($name, $skip);
        $phar->addFromString($localName, $str);
    }
}

$stub = <<<EOF
#!/usr/bin/env
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

Phar::mapPhar('fluid.phar');

require 'phar://fluid.phar/bootstrap.php';

__HALT_COMPILER();

EOF;


$phar->setStub(ltrim($stub, file_get_contents('bootstrap.php')));
