<?php
require_once 'request.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Сокращение ссылок</title>
    <link rel="stylesheet" href="css/style.css">

</head>
<body>
<h1>Сервис сокращения ссылок:</h1>
<form action="" method="GET">

    <p>
        <input type="text" value="<?= $_GET['url']; ?>" name="url" placeholder="Вставьте ссылку" required/>
    </p>
    <button class="btn" type="submit">Сократить</button>

    <p>
        <? if (!(empty($_GET['error']))) {
            echo $_GET['error'];
        } ?>
    </p>
    <p>
        <?
        $str = $_GET['url'];
        $str = explode('/', $str);
        $img_name = $str[1];
        if (file_exists(__DIR__ . '/qr/' . $img_name . '_qr.png')) {
            echo '<img src = "qr/' . $img_name . '_qr.png">';
        }
        ?>
    </p>

</form>
</body>
</html>
