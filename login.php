<?php
session_start();
require_once "db.php";

checkAccess(['banned', 'user']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' & isset($_POST['log'])) {
    if ($_POST['nickname'] === "" || $_POST['password'] === "") {
        echo "пусто";
    } else {
        $nickname = $db->real_escape_string($_POST['nickname']);
        $password = $db->real_escape_string($_POST['password']);
        $stmt = $db->prepare("SELECT * FROM users WHERE nickname = ?");
        $stmt->bind_param('s', $nickname);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['nickname'] = $user['nickname'];
                $_SESSION['role'] = $user['role'];
                header("Location: /board/");
                exit;
            }
        } else {
            echo "юезр не зарган или неправильный логин/пароль";
        }   
    }
}
?>

<body>
    <a href="/board/register.php">зарегаться</a>
    <form method="POST">
        <input type="text" name="nickname" placeholder="ник" required>
        <input type="text" name="password" placeholder="пароль" required>
        <button type="submit" name="log">войти</button>
    </form>
</body>