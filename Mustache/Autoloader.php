<?php
namespace Mustache;

require "Singleton.php";

class Autoloader
{
	use Singleton;

	public function autoload($class)
	{
		require_once(str_replace("\\", "/", $class) . ".php");
	}

	public function register()
	{
		spl_autoload_register("self::autoload");
	}
}