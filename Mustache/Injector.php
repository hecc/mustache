<?php
namespace Mustache;

class Injector {

	private $cache;
	private $factory;

	const INSTANTIATING = "INSTANTIATING";

	public function __construct(&$cache, $factory) {
		$this->cache = &$cache;
		$this->factory = $factory;
	}

	public function addToCache($name, $obj) {
		$this->cache[$name] = $obj;
	}

	public function get($serviceName) {
		if(isset($this->cache[$serviceName])) {
			if($this->cache[$serviceName] == self::INSTANTIATING)
				throw new \Exception("Circular dependency!");
			return $this->cache[$serviceName];
		} else {
			$this->cache[$serviceName] = self::INSTANTIATING;
			$factory = $this->factory;
			return $this->cache[$serviceName] = $factory($serviceName);
		}
	}


	public function invoke($callable, $locals = null) {

		if(is_string($callable)) {
			$fn = explode(":", $callable);
			$callable = array($this->get($fn[0]), $fn[1]);
		}

		if(is_array($callable))
			$rf = new \ReflectionMethod($callable[0], $callable[1]);
		else
			$rf = new \ReflectionFunction($callable);

		$args = array();

		foreach ($rf->getParameters() as $key => $value) {
			if(is_array($locals) && isset($locals[$value->name]))
				$args[] = $locals[$value->name];
			else
				$args[] = $this->get($value->name);
		}

		switch(count($args)) {
			case 0: return $callable();
			case 1: return $callable($args[0]);
			case 2: return $callable($args[0], $args[1]);
			case 3: return $callable($args[0], $args[1], $args[2]);
			case 4: return $callable($args[0], $args[1], $args[2], $args[3]);
			case 5: return $callable($args[0], $args[1], $args[2], $args[3], $args[4]);
		}

		return call_user_func_array($callable, $args);
	}

	public function create($class, $locals = null) {
		$rc = new \ReflectionClass($class);
		$rf = $rc->getConstructor();
		if($rf) {
			$params = $rf->getParameters();
			$args = array();

			foreach ($params as $key => $value) {
				if(is_array($locals) && isset($locals[$value->name]))
					$args[] = $locals[$value->name];
				else
					$args[] = $this->get($value->name);
			}

			return $rc->newInstanceArgs($args);
		}
		
		return new $class;
	}
}