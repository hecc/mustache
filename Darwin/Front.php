<?php
namespace Darwin;

class Front {
	private $front = array();
	private $response;
	private $injector;

	public function __construct($injector) {
		$this->injector = $injector;
	}

	public function map($method, $route, $callback) {
		$match = false;

		$ors = explode('|', $route);

		foreach ($ors as $or) {
			$ok = true;

			$vars = array();
			parse_str($or, $vars);

			foreach ($vars as $key => $val) {
				$ok &= ($key == '*')
					|| (substr($key, 0, 1) == '!' && !isset($_GET[substr($key, 1)]))
					|| (isset($_GET[$key]) && ($val == '*' || $_GET[$key] == $val));
			}

			$match |= $ok && (count($vars) > 0 || (count($vars) == 0 && count($_GET) == 0));
		}

		if ($match)
			array_push($this->front, $callback);
		else
			return false;

		return true;
	}

	public function run() {
		$this->scope = new \stdClass;
		$this->next();
	}

	public function next() {
		$callable = array_shift($this->front);
		
		if($callable)
			$this->injector->invoke($callable, array("scope" => $this->scope, "next" => array($this, "next")));
	}
}