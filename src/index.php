<?php

require_once '../vendor/autoload.php';

$server   = '192.168.80.2';
$port     = 8883;
$clientId = 'infoscreen';

$mqtt = new \PhpMqtt\Client\MqttClient($server, $port, $clientId);
$connectionSettings = (new \PhpMqtt\Client\ConnectionSettings)
    ->setUsername('infoscreen')
    ->setPassword('5k1nnyL4773')
    ->setUseTls(true)
    ->setTlsSelfSignedAllowed(true)
    ->setTlsCertificateAuthorityFile('../certs/192.168.95.115/ca-root-cert.crt')
    ->setTlsClientCertificateFile('../certs/192.168.80.2_new/client.crt')
    ->setTlsClientCertificateKeyFile('../certs/192.168.80.2_new/client.key');

try {
	$mqtt->connect($connectionSettings, true);
} catch (\PhpMqtt\Client\Exceptions\ConfigurationInvalidException $e) {
	echo $e;
	exit();
} catch (\PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException $e) {
	echo $e;
	exit();
}

$topic = '/hospotal/';

try {
	$mqtt->subscribe('php-mqtt/client/test', function ($topic, $message) {
		echo sprintf("Received message on topic [%s]: %s\n", $topic, $message);
	}, 0);
} catch (\PhpMqtt\Client\Exceptions\DataTransferException $e) {
	echo $e;
	exit();
} catch (\PhpMqtt\Client\Exceptions\RepositoryException $e) {
	echo $e;
	exit();
}


try {
	$mqtt->loop(true);
} catch (\PhpMqtt\Client\Exceptions\DataTransferException $e) {
	echo $e;
	exit();
} catch (\PhpMqtt\Client\Exceptions\InvalidMessageException $e) {
	echo $e;
	exit();
} catch (\PhpMqtt\Client\Exceptions\ProtocolViolationException $e) {
	echo $e;
	exit();
} catch (\PhpMqtt\Client\Exceptions\MqttClientException $e) {
	echo $e;
	exit();
}

try {
	$mqtt->disconnect();
} catch (\PhpMqtt\Client\Exceptions\DataTransferException $e) {
	echo $e;
	exit();
}


