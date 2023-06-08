<?php

require_once 'conn.php';

$data = json_decode(file_get_contents('php://input'), true);

$url = $data['url'];

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
   
}

$data = array('url'=> $url, 'short_url' => $_SERVER['SERVER_NAME'] . '/' . $url_short);
header('Content-Type: application/json');
echo json_encode($data);

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