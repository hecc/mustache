<?php
require "bootstrap.php";


echo "<pre>";



$app = \Mustache\AppContainer::getInstance();


$app->inject("db", "\Mustache\PdoAdapter")
	->config(function () { $this->connect("mysql:dbname=mustache;host=127.0.0.1", "root", ""); });


$app->inject("front", "\Mustache\Front")
	->config(function () {
		$this->map("GET", "*", function ($factory) {
			$p = $factory("\Mustache\Post");
			// $p->color = array("red", "blue");
			// $p->save();
			// $p->load(array("data" => '{"color":["red","blue"]}'));
			// var_dump($p->color[0]);
		});
	});

$app->run(function ($front) { $front->run(); });



printf("\n%.4fs", microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]);
echo "</pre>";
