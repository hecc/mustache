<?php
namespace Darwin;

class Provider {
	private $class;
	private $configFn;


	public function __construct($class) {
		$this->class = $class;
	}

	public function config($fn) {
		$this->configFn = $fn;
	}

	public function instantiate($injector) {
		$obj = $injector->create($this->class);

		if(is_callable($this->configFn)) {
			$fn = $this->configFn->bindTo($obj, $obj);
			$injector->invoke($fn);
		}

		return $obj;
	}

}