<?php
class DB {
    private $hostname = "localhost"; // Check your MySQL configuration. It may be different.
    private $username = "root"; // Should be different if database is owned by another user.
    private $password = ""; // Password is blank by default on XAMPP.
    private $database = ""; /* If specified, allows you to perform queries without `database.table` format. However, you can only perform queries on that database which is specified. */
    // You can use the charset variable however for PHP <= 5.3.6 the charset option was ignored.
    function __construct() {
        $this->pdo = $this->connect();
    }

    protected function connect() {
        ini_set('display_errors', 1); // Turn on displaying errors.
        // data source name
        $dsn = "mysql:host=".$this->hostname.";dbname=".$this->database; //This is where the charset variable is used.
        $opt = [ // Options to modify PDO connection attributes
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //https://phpdelusions.net/pdo#errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //https://phpdelusions.net/pdo#fetch
			PDO::ATTR_EMULATE_PREPARES   => TRUE //https://phpdelusions.net/pdo#emulation
        ];
        $pdo = new PDO($dsn, $this->username, $this->password, $opt); // PDO connection.
        //$pdo->exec("SET NAMES utf8mb4"); // Optionally define charset for PHP <= 5.3.6
        return $pdo;
    }

    function pdo($sql, $params = array()) {
        if (!$params) {
            $stmt = $this->pdo->query($sql);
        } else {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
        }
		return $stmt;
    }
}
?>
