<?php
namespace Mustache;

class PdoAdapter
{
	private $connection;

	public function connect($dsn = "mysql:dbname=darwin;host=localhost", $user = "root", $password = "root") {
		$this->connection = new \PDO($dsn, $user, $password);
	}

	public function conn() {
		return $this->connection;
	}
}