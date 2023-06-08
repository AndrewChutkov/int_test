<?php

require_once 'conn.php';
require_once __DIR__ . '/phpqrcode/qrlib.php';

$url = trim($_GET['url']);

if (isset($_GET['url'])) {

    // Валидация ссылки
    if (filter_var($_GET['url'], FILTER_VALIDATE_URL)) {

        // Проверка на наличие созданного короткого url'а в бд
        $tmp_url_short = false;
        $url_short = '';

        while (!$tmp_url_short) {

            $url_short = url_short_gen();

            // Так и не разобрался, почему бд нужно выбирать принудительно, так что это такой костыль
            $dbh->exec('USE ' . $db_name . ';');

            $sth = $dbh->prepare("SELECT * FROM `urls` WHERE `url_short` = ?");
            $sth->execute(array($url_short));

            $rowCount = $sth->rowCount();

            if (!$rowCount) {
                $tmp_url_short = true;
                break;
            }
        }

        // Добавление короткой ссылки в бд
        if ($tmp_url_short) {

            $sth = $dbh->prepare("INSERT INTO `urls` SET `url` = :url, `url_short` = :short_url");
            $sth->execute(array('url' => $url, 'short_url' => $url_short));

            if ($sth) {
                $_GET['url'] = $_SERVER['SERVER_NAME'] . '/' . $url_short;
            } else {

                // Вообще, ссылка сгенерирована, просто в бд не добавлена,
                // но вряд ли такой текст даже теоретически нужно выводить пользователю
                $_GET['error'] = 'Ссылка не сгенерирована!';
            }
            // Создаем qr-код
            QRcode::png($url, __DIR__ . '/qr/' . $url_short . '_qr.png');
        }
    } else {
        $_GET['error'] = 'Это не ссылка!';
    }
} else {

    $URI = $_SERVER['REQUEST_URI'];

    $url_short = substr($URI, 1);

    if (iconv_strlen($url_short)) {

        $sth = $dbh->prepare("SELECT * FROM `urls` WHERE `url_short` = ?");
        $sth->execute(array($url_short));

        $rowCount = $sth->rowCount();

        if ($rowCount) {
            $row = $sth->fetch(PDO::FETCH_ASSOC);

            header("Location: " . $row['url']);

            $url = $row['url'];

        }
    }
}

$conn = null;

// Функция создания короткой ссылки
function url_short_gen($min = 5, $max = 8)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDFEGHIJKLMNOPRSTUVWXYZ0123456789';
    $new_chars = str_split($chars);

    $url_short = '';
    $rand_end = mt_rand($min, $max);

    for ($i = 0; $i < $rand_end; $i++) {
        $url_short .= $new_chars[mt_rand(0, sizeof($new_chars) - 1)];
    }

    return $url_short;
}
