<?php

use CodeIgniter\Boot;
use Config\Paths;

//---------------------------------------------------------------
// CHECK PHP VERSION
//---------------------------------------------------------------
$minPhpVersion = '8.1';

if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    exit(sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION
    ));
}

//---------------------------------------------------------------
// SET THE CURRENT DIRECTORY
//---------------------------------------------------------------
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

//---------------------------------------------------------------
// BOOTSTRAP THE APPLICATION
//---------------------------------------------------------------
require FCPATH . '../app/Config/Paths.php';

$paths = new Paths();

require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));
