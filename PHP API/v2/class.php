<?php

class Models{

	//DB connection var
	public $db;

	public function __construct($conn){
		$this->db = $conn;
	}

	public function add($object, $data){
		$str1 = "";
		$str2 = "";

		foreach($data as $k => $v){
			$str1 .= "`$k`,";
			$str2 .= "'$data',";
		}
		$str1 = trim($str1, ",");
		$str2 = trim($str2, ",");
		$sql = "INSERT INTO `$object` ($str1) VALUES ($str2)";
		$insert = $this->db->query($sql);
	}

	public function edit($object, $data, $id){
		$str = "";

		foreach($data as $k => $v){
			$str .= "`$k` = '$data',";
		}
		$str = trim($str, ",");
		$sql = "UPDATE `$object` SET $str WHERE `id` = '$id'";
		$update = $this->db->query($sql);
	}

	public function get($object, $id){
		$sql = "SELECT * FROM `$object` WHERE `id` = `$id`";
		$get = $this->db->query($sql);
		return $get;
	}

	public function delete($object, $id){
		$sql = "DELETE FROM `$object` WHERE `id` = `$id`";
		$delete = $this->db->query($sql);
	}
}

?>