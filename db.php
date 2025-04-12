<?php

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'board');

$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($db -> connect_errno) {
    echo "failed" . $db->connect_errno;
    exit;
}

function checkAccess($role) {
    if (!isset($_SESSION['role'])) {
        header("Location: /board/login.php");
        exit;
    }
}
?>