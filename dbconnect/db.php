<?php
class DB {
	private $host = "localhost"; //Check your MySQL configuration. It may be different.
	private $user = "root"; //Should be different if database is owned by another user.
	private $password = ""; //Password is blank by default on XAMPP.
	private $database = "database"; //Allows you to perform queries without `database.table` format.

	function __construct() {
		$this->mysqli = $this->connect();
	}

	protected function connect() {
		ini_set('display_errors',1); // Turn on displaying errors
		error_reporting(E_ALL); // Set error level
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Set report functions
		return new mysqli($this->host,$this->user,$this->password,$this->database);
	}
    //https://phpdelusions.net/mysqli/simple
    function mysqli($sql, $params = array(), $types = "") {
        if (!$params) {
            return $this->mysqli->query($sql);
        } else {
            $stmt = $this->mysqli->prepare($sql);
            if (!$types) $types = $types ?: str_repeat("s", count($params)); // Default to string if undefined
            if (version_compare(PHP_VERSION, '5.6') >= 0) {
                $stmt->bind_param($types, ...$params); //bind_param using spread operator
            } else {
                $bind_array = array($types);
                foreach ($params as $key => $value) {
                    $bind_array[] = &$params[$key];
                }
                call_user_func_array(array($stmt, 'bind_param'), $bind_array);
            }
            $stmt->execute();
            return $stmt;
        }
    }
}
?>
