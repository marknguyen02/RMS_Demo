<?php

declare(strict_types=1);

use Src\System\DatabaseConnector;

function rglob(string $pattern, int $flags = 0): array
{
	$files = glob($pattern, $flags);

	foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
		$files = array_merge(
			[],
			...[$files, rglob($dir . "/" . basename($pattern), $flags)]
		);
	}
	return $files;
}

//include all files in project
foreach (rglob("src/*.php") as $filename) {
	include $filename;
}

$DB_SETTINGS = array(
	'DB_HOST' => 'localhost',
	'DB_PORT' => '80',
	'DB_DATABASE' => 'rms',
	'DB_USERNAME' => 'root',
	'DB_PASSWORD' => ''
);
$dbConnection = (new DatabaseConnector($DB_SETTINGS))->getConnection();
