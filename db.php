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
    global $db;
    if (!isset($_SESSION['role'])) {
        if ($_SERVER['PHP_SELF'] !== '/board/login.php' && $_SERVER['PHP_SELF'] !== '/board/register.php') {
            header("Location: /board/login.php");
            exit;
        }
    } else {
        $stmt = $db->prepare('SELECT * FROM users WHERE nickname = ?');
        $stmt->bind_param('s', $_SESSION['nickname']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        if ($_SESSION['role'] !== $user['role']) {
            $_SESSION['role'] = $user['role'];
        }
        if ($_SESSION['role'] === "banned" && $_SERVER['PHP_SELF'] !== '/board/ban.php') {
            header("Location: /board/ban.php");
            exit;
        }
        if (!in_array($_SESSION['role'], $role)) {
            header("Location: /board/");
            exit;
        }
    }
}
?>