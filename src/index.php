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
    ->setTlsCertificateAuthorityFile('../certs/192.168.80.2_new/ca-root-cert.crt')
    ->setTlsClientCertificateFile('../certs/192.168.80.2_new/client.crt')
    ->setTlsClientCertificateKeyFile('../certs/192.168.80.2_new/client.key');
$mqtt->connect($connectionSettings, true);
$topic = '/hospotal/';
$mqtt->subscribe('php-mqtt/client/test', function ($topic, $message) {
    echo sprintf("Received message on topic [%s]: %s\n", $topic, $message);
}, 0);
$mqtt->loop(true);
$mqtt->disconnect();


