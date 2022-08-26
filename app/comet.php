<?php
require_once 'vendor/autoload.php';

$app = new Comet\Comet([
	'host' => '127.0.0.1',
	'port' => 8080,
]);

$app->get('/json',
	function ($request, $response) {
		$file = 'mqtt.csv';
		$f = fopen($file, 'r');
		$data = [];
		while (($row = fgetcsv($f)) !== false) {
			$data[] = $row;
		}
		fclose($f);
		return $response
			->with($data);
	});

$app->run();