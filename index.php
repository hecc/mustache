<?php
require "bootstrap.php";


echo "<pre>";



$app = \Darwin\AppContainer::getInstance();


$app->inject("db", "\Darwin\PdoAdapter")
	->config(function () { $this->connect(); });


$app->inject("front", "\Darwin\Front")
	->config(function () {
		$this->map("GET", "*", function ($factory) {
			$p = $factory("\Darwin\Post");
			// $p->color = array("red", "blue");
			// $p->save();
			$p->load(array("data" => '{"color":["red","blue"]}'));
			var_dump($p->color[0]);
		});
	});

$app->run(function ($front) { $front->run(); });



printf("\n%.4fs", microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]);
echo "</pre>";
