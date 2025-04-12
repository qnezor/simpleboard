<?php
session_start();
require_once "db.php";

session_unset();
session_destroy();

header("Location: /board/");
exit;
?>