<?php
require_once './vendor/autoload.php';

$client          = new \StevenBuehner\Controller\ChurchToolsPasswordClient(
	$ctUrl = 'https://XXXXXXX.church.tools',
	$ctEmail = 'test@test.de',
	$ctPassword = 'XXXXXXX'
);
$allowed_domains = [
	'http://localhost:8081',
	'http://localhost:8080',
];
$proxy           = new \StevenBuehner\Proxy\ChurchToolsProxy($client, $allowed_domains);

$proxy->run();

