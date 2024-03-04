<?
    session_start();
    require_once('functions.php');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <!-- База -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Иконка с названием -->
    <link rel="shortcut icon" href="images/icons/company_icon.png">
    <title>Скачай наше приложение «Вкусно – и точка!» и получи скидку 10%.</title>

    <!-- Стили страницы -->
    <link rel='stylesheet' type='text/css' href='fonts/sansation/stylesheet.css'>
    <link rel='stylesheet' type='text/css' href='styles/reset_style.css'>
    <link rel='stylesheet' type='text/css' href='styles/basic.css'>
    <link rel='stylesheet' type='text/css' href='styles/application.css'>
</head>

<body>
    <!-- Шапка страницы -->
    <? require_once('header.php') ?>

    <!-- Основное тело -->
    <main style="margin-top: 90px">

        <div class="gradient-line"></div> <!-- Линия с градиентом -->

        <!-- Контент страницы приложения -->
        <div class="mockup-application">
            <div class="container">

                <!-- Текст приложения -->
                <div class="text-application">
                    <h1>Загрузи наше приложение!</h1>
                    <p>С помощью мобильного приложения «Вкусно — и точка» каждый желающий может легко и быстро оформить заказ,<br>получить хорошую скидку, а также одним из первых узнавать полезную информацию о сети быстрого питания.</p>
                </div> <!-- /.text-application -->

                <!-- QR-код приложения «Вкусно – и точка!» -->
                <img src="images\page-elements\app-download-products.png" class="application-qr-code" alt="QR-код «Вкусно – и точка!»">

                <!-- Текст приложения -->
                <div class="text-application">
                    <p>Установка программы доступна бесплатно обладателям современных смартфонов на базе Android или iOS.<br>Найти приложение можно в официальных магазинов контента в зависимости от вашего устройства.</p>
                </div> <!-- /.text-application -->

                <!-- Ссылки для скачивания -->
                <div class="mockup-installer">

                    <!-- Ссылка Google Play -->
                    <a href="https://play.google.com/store/apps/details?id=com.apegroup.mcdonaldsrussia" target="_blank">
                        <img src="images\icons\icon-google.png" alt="Установка Google Play">
                    </a>

                    <!-- Ссылка App Store -->
                    <a href="https://apps.apple.com/RU/app/id896111038" target="_blank">
                        <img src="images\icons\icon-apple.png" alt="Установка App Store">
                    </a>

                    <!-- Ссылка AppGallery -->
                    <a href="https://appgallery.huawei.com/#/app/C102465481" target="_blank">
                        <img src="images\icons\icon-AppGaller.png" alt="Установка AppGallery">
                    </a>

                </div> <!-- /.mockup-installer -->
                
            </div> <!-- /.container -->
        </div> <!-- /.mockup-application -->

        <div class="gradient-line"></div> <!-- Линия с градиентом -->

    </main> <!-- /.main -->

    <!-- Подвал страницы -->
    <? require_once('footer.php') ?>
</body>