<?php

include __DIR__ . '/vendor/autoload.php';

use SparkPHP\App;

$app = new App();

include './config.php';

$app -> add('GET', '/', function($request, $response) {
	return $response -> render('home');
});

$app -> run();
