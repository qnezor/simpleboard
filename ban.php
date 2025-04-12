<?php
session_start();
require_once "db.php";
checkAccess(['banned', 'admin']);
?>

<style>
    * {
        margin: 0;
        padding: 0;
    }
    body {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    img {
        width: 200px;
        height: 200px;
    }
</style>

<head>
    <title>забанен, чекай</title>
</head>

<body>
    <img src="/board/assets/server/nightchat.webp">
    <p>вы были забанены в ночном чате</p>
</body>