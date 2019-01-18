<?php
ini_set('display_errors', 1); // Turn on displaying errors.
// data source name
$dsn = "mysql:host=localhost";
$opt = [ // Options to modify PDO connection attributes
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //https://phpdelusions.net/pdo#errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //https://phpdelusions.net/pdo#fetch
    PDO::ATTR_EMULATE_PREPARES   => TRUE //https://phpdelusions.net/pdo#emulation
];
$pdo = new PDO($dsn, 'root', '', $opt); // PDO connection.
//$pdo->exec("SET NAMES utf8mb4"); // Optionally define charset for PHP <= 5.3.6
function pdo($pdo, $sql, $params = array()) {
    if (!$params) {
        $stmt = $pdo->query($sql);
    } else {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }
    return $stmt;
}
?>
