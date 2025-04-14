<?php
session_start();
require_once "db.php";

checkAccess(['user', 'admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['shitpost'])) {
    if ($_SESSION['role'] === 'banned') {
        header("Location: /board/banned.php");
        exit;
    } else {
        $nickname = htmlspecialchars($_SESSION['nickname']);
        $image = "<img src=" . htmlspecialchars($_POST['image']) . ">";
        $reply = $db->real_escape_string((int)$_POST['reply']);
        $text = $db->real_escape_string($_POST['text']);
        if ($image !== "") {
            $text = $image . "<br>" . $db->real_escape_string($_POST['text']);
        }
        $insert = $db->query("INSERT INTO messages (nickname, text, reply) VALUES ('$nickname', '$text', $reply)");
        header("Location: /board/");
        exit;
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $db->query("DELETE from messages WHERE id=$id");
    header("Location: /board/");
    exit;
}

if (isset($_GET['ban'])) {
    $nick = $_GET['ban'];
    $db->query("UPDATE users SET role='banned' WHERE nickname='$nick'");
    header("Location: /board/");
    exit;
}

if (isset($_GET['unban'])) {
    $nick = $_GET['unban'];
    $db->query("UPDATE users SET role='user' WHERE nickname='$nick'");
    header("Location: /board/");
    exit;
}

$messages = $db->query("SELECT * FROM messages ORDER BY time DESC");
$users = $db->query("SELECT * FROM users");
?>

<head>
    <title>simpleboard engine</title>
</head>

<style>
    * {
        margin: 0;
        padding: 0;
    }

    body {
        padding: 5px;
    }
</style>

<body>
    <?php if (isset($_SESSION['nickname'])): ?>
        <a href="/board/logout.php">выйти</a>
    <?php else: ?>
        <a href="/board/login.php">вход</a>
        <a href="/board/register.php">рега</a>
    <?php endif; ?>
    <form method="POST" style="margin: 5px 0px;">
        <input type="text" name="nickname" value="<?php echo $_SESSION['nickname']; ?>" disabled>
        <input type="text" name="reply" value="" placeholder="ответ" id="reply_inp">
        <div>
            <input type="text" name="image" placeholder="ссылка на картинку">
        </div>
        <div style="display: flex;">
            <textarea type="text" name="text" placeholder="текст" style="height: 100px; width: 200px;" required></textarea>
            <button type="submit" name="shitpost">отправить</button>
        </div>
    </form>
    <hr style="border: 3px solid black;">
    <?php if ($messages->num_rows === 0): ?>
        <p>сообщений нет</p>
    <?php else: ?>
        <?php while ($message = $messages->fetch_assoc()): ?>
            <div id="<?php echo $message['id'] ?>">
                <div style="display: flex;">
                    <p style="font-size: 80%; margin-top: 3px">#<?php echo $message['id'] ?> | <?php echo $message['time'] ?></p>
                </div>
                <?php
                $stmt = $db->prepare("SELECT * FROM users WHERE nickname = ?");
                $stmt->bind_param('s', $message['nickname']);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    $role = $user['role'];
                } else {
                    $role = "anon";
                }
                ?>
                <?php if ($role === 'admin'): ?>
                    <p style="color: blue"><?php echo $message['nickname'] ?> (админ)</p>
                <?php elseif ($role === "banned"): ?>
                    <p style="color: red"><?php echo $message['nickname'] ?> (забанен)</p>
                <?php else: ?>
                    <p><?php echo $message['nickname'] ?></p>
                <?php endif; ?>
                <?php
                $stmt = $db->prepare("SELECT * FROM messages WHERE id = ?");
                $stmt->bind_param('i', $message['reply']);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    $nick_reply = $user['nickname'];
                }
                ?>
                <?php if ($message['reply'] > 0): ?>
                    <p>ответ на сообщение: <a href="#<?php echo $message['reply'] ?>">#<?php echo $message['reply'] ?> | <?php echo $nick_reply ?></a></p>
                <?php endif; ?>
                <p><?php echo $message['text'] ?></p>
                <div>
                    <button onclick="reply(<?php echo $message['id'] ?>)">ответить</button>
                    <?php if (isset($_SESSION['role']) and ($_SESSION['role']) === 'admin'): ?>
                        <a href="/board?delete=<?php echo $message['id'] ?>" onclick="return confirm('are you sure about that?')">удалить</a>
                        <?php if ($role !== 'admin'): ?>
                            <?php if ($role === 'banned'): ?>
                                <a href="/board?unban=<?php echo $message['nickname'] ?>" onclick="return confirm('are you sure about that?')">рабанить</a>
                            <?php else: ?>
                                <a href="/board?ban=<?php echo $message['nickname'] ?>" onclick="return confirm('are you sure about that?')">забанить</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <hr style="border: 1px solid black;">
        <?php endwhile; ?>
    <?php endif; ?>
    <div style="text-align: center; margin-top: 5px; display: flex; flex-direction: column; align-items: center;">
        <img src="/board/assets/server/nightchat.webp" style="width: 150px; height: 150px">
        <a href="https://github.com/qnezor/simpleboard" target="_blank">simpleboard engine, beta 0.5</a>
    </div>
</body>

<script>
    function reply(id) {
        document.getElementById('reply_inp').value = id;
    }
</script>