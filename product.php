<?
    session_start();
    require_once('functions.php');

    if (!empty($_GET['id']))
    {
        $product = get_product($_GET['id']);
    }
    else
    {
        header("Location: /index.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Вкусно - и точка: <?=$product['name']?></title>
    </head>
    <body style="display: flex; flex-direction: column; justify-content: center; align-items: center">
        <div style="background-color: purple; width: 250px; height: 250px;">Image</div>
        <div>Имя: <?=$product['name']?></div>
        <div>Описание: <?=$product['description']?></div>
        <div>Цена: <?=$product['price']?></div>
        <hr style="width: 100%">
        <h4>Пищевая ценность</h4>
        <div>Вес: <?=$product['weight']?> г</div>
        <div>Энергетическая ценность: <?=ceil($product['ccal'])?> Ккал / <?=ceil($product['ccal'] * 4.1868)?> кДж</div>
        <hr style="width: 100%">
        <h4>В порции</h4>
        <div>Белки: <?=$product['proteins']?> г</div>
        <div>Жиры: <?=$product['fats']?> г</div>
        <div>Углеводы: <?=$product['carbohydrates']?> г</div>
        <hr style="width: 100%">
        <h4>Состав</h4>
        <? foreach ($product['ingredient'] as $elem): ?>
        <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center">
            <div style="font-weight: bold"><?=$elem['name']?></div>
            <div style="font-size: 12px"><?=$elem['compound']?></div>
        </div>
        <? endforeach ?>
    </body>
</html>
<? require_once('buttons.php'); ?>