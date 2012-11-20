<?php
namespace Darwin;

class Post {
	protected $db;
	protected $data;

	protected $baseFields = ["id", "id_language", "id_parent", "taxonomy", "type", "slug", "code", "title"];
	protected $defaults = ["taxonomy" => "post"];
	protected $indexes = ["color"];

	public function __construct($db) {
		$this->db = $db->conn();
	}

	public function load($item)
	{
		$data = json_decode($item["data"], true);
		unset($item["data"]);

		$this->data = $data ? array_merge($data, $item) : $item;

		return $this;
	}

	public function save()
	{
		$values = [];
		foreach($this->baseFields as $baseField) {
			if(array_key_exists($baseField, $this->data)) {
				if(is_null($this->data[$baseField]))
					$values[$baseField] = isset($this->defaults[$key]) ? $this->defaults[$key] : "NULL";
				else
					$values[$baseField] = $this->db->quote($this->data[$baseField]);
			}
		}	

		if(isset($values["id"]) && $values["id"] == "NULL")
			unset($values["id"]);

		$data = array();

		$toIndex = [];

		foreach($this->data as $k => $v) {
			if(!in_array($k, $this->baseFields)) {
				$data[$k] = $v;

				if(in_array($k, $this->indexes))
					$toIndex[$k] = $v;
			}
		}

		$values["data"] = $this->db->quote(json_encode($data));

		$sql = sprintf("INSERT INTO post (%s) VALUES (%s)", join(", ", array_keys($values)), join(", ", $values));
		$sql .= " ON DUPLICATE KEY UPDATE " . join(", ", array_map(function($v, $k) { return "$k=$v"; }, $values, array_keys($values)));

		echo $sql;

		$this->db->exec($sql);
		if(!isset($values["id"])) $this->data["id"] = $this->db->lastInsertId();


		foreach ($toIndex as $key => $value) {
			if(is_array($value)) {
				foreach($value as $val) {
					$val = $this->db->quote($val);
					$sql = sprintf("INSERT INTO index_%s (id_post, value) VALUES (%d, %s) ON DUPLICATE KEY UPDATE value=%s", $key, $this->data["id"], $val, $val);
					$this->db->query($sql);	
				}
			} else {
				$value = $this->db->quote($value);
				$sql = sprintf("INSERT INTO index_%s (id_post, value) VALUES (%d, %s) ON DUPLICATE KEY UPDATE value=%s", $key, $this->data["id"], $value, $value);
				$this->db->query($sql);
			}
		}

		return $this;
	}

	public function __set($name, $value) {
		$this->data[$name] = $value;
	}

	public function __get($name) {
		return $this->data[$name];
	}

}