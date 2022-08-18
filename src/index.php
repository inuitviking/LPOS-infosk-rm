<?php

require_once '../vendor/autoload.php';

//$server   = '192.168.80.2';
$server		= 'mqtt';
$port		= 8883;
$clientId = 'infoscreen';

echo $server."\n";
echo $port."\n";
echo $clientId."\n";

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
} catch (\PhpMqtt\Client\Exceptions\ConfigurationInvalidException|\PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException $e) {
	echo $e;
	exit();
}

try {
	$mqtt->subscribe('/hospital/', function ($topic, $message) {
		echo sprintf("Received message on topic [%s]: %s\n", $topic, $message);
	}, 0);
} catch (\PhpMqtt\Client\Exceptions\DataTransferException|\PhpMqtt\Client\Exceptions\RepositoryException $e) {
	echo $e;
	exit();
}


try {
	$mqtt->loop(true);
} catch (\PhpMqtt\Client\Exceptions\DataTransferException|\PhpMqtt\Client\Exceptions\InvalidMessageException|\PhpMqtt\Client\Exceptions\ProtocolViolationException|\PhpMqtt\Client\Exceptions\MqttClientException $e) {
	echo $e;
	exit();
}

try {
	$mqtt->disconnect();
} catch (\PhpMqtt\Client\Exceptions\DataTransferException $e) {
	echo $e;
	exit();
}


