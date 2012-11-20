<?php
namespace Darwin;

class AppContainer {
	use Singleton;

	private $services = array();
	private $injectorCache = array();
	private $injector;

	public function init() {

		// register_shutdown_function(array($this, "serve"));

		$this->injector = new \Darwin\Injector($this->injectorCache, 
			function ($serviceName) {
				if(!isset($this->services[$serviceName]))
					throw new \Exception("Unknown service: $serviceName");

				$provider = $this->services[$serviceName];
				$obj = $provider->instantiate($this->injector);
				// $this->injector->invoke(array($obj, "init"));

				return $obj;
			});

		$this->injectorCache["injector"] = $this->injector;
		$this->injectorCache["factory"] = function ($class) { return $this->injector->create($class); };

	}

	public function inject($name, $class) {
		return $this->services[$name] = new Provider($class);
	}

	public function config($fn) {
		$this->injector->invoke($fn);
	}

	public function run($fn) {
		$this->injector->invoke($fn);
	}
}