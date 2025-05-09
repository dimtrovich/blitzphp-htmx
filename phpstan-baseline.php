<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	// identifier: function.notFound
	'message' => '#^Function site_url not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Http/Redirection.php',
];
$ignoreErrors[] = [
	// identifier: new.static
	'message' => '#^Unsafe usage of new static\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Http/Request.php',
];
$ignoreErrors[] = [
	// identifier: new.static
	'message' => '#^Unsafe usage of new static\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Http/Response.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
