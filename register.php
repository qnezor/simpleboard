<?php
session_start();
require_once "db.php";

checkAccess('banned', 'user');

if ($_SERVER['REQUEST_METHOD'] === 'POST' & isset($_POST['reg'])) {
    if (!isset($_POST['nickname']) === "" || !isset($_POST['password']) === "") {
        echo "пусто";
    } else {
        $nickname = $db->real_escape_string($_POST['nickname']);
        $password = $db->real_escape_string($_POST['password']);
        $stmt = $db->prepare("SELECT * FROM users WHERE nickname = ?");
        $stmt->bind_param('s', $nickname);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo "занято";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $db->query("INSERT INTO users (nickname, password, role) VALUES ('$nickname', '$hash', 'user')");
            $user_id = $db->insert_id;
            $_SESSION['nickname'] = $nickname;
            $_SESSION['role'] = 'user';
            header("Location: /board/");
            exit;
        }   
    }
}
?>

<body>
    <a href="/board/login.php">войти</a>
    <form method="POST">
        <input type="text" name="nickname" placeholder="ник" required>
        <input type="text" name="password" placeholder="пароль" required>
        <button type="submit" name="reg">войти</button>
    </form>
</body>