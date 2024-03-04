<?
    session_start();
    require_once('functions.php');
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Тестовая страница</title>
    </head>
    <body>
        <h3>Тестовая страница</h3>
        <hr>
        Вывод сообщений:
        <pre style="border: 1px solid black; background-color: #D9D9D9">
        Рандомный купон:
        <?
            var_export($_SERVER);
        ?>
        </pre>
        <hr>
        Сессия
        <pre style="border: 1px solid black; background-color: #D9D9D9">
        <?
            var_dump($_SESSION);
        ?>
        </pre>
        <hr>
        generate_random_coupon
        <pre style="border: 1px solid black; background-color: #D9D9D9">
        <?
            var_dump(true); 
        ?>
        </pre>
    </body>
</html>