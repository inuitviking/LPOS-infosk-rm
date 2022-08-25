<?php
require_once '../vendor/autoload.php';

$app = new Comet\Comet([
	'host' => '127.0.0.1',
	'port' => 8080,
]);

$app->get('/json',
	function ($request, $response) {
		$data = [ "message" => "Hello, Comet!" ];
		return $response
			->with($data);
	});

$app->run();