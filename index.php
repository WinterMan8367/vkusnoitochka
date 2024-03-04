<?
    session_start();
    require_once('functions.php');

    if (empty($_GET['category']))
    {
        header('Location: /?category=news');
        exit;
    }

    $statuses = [1 => 'news', 'popular'];
    $categories = [1 => 'burgers', 'drinks', 'snacks', 'desserts', 'sauces', 'salads-and-rolls'];

    if (array_search($_GET['category'], $categories))
    {
        $category = array_search($_GET['category'], $categories);
    }
    else
    {
        $status = array_search($_GET['category'], $statuses);
    }

    if (!empty($_POST['counter']))
    {
        if ($_POST['counter'] > 0)
        {
            if (empty($_SESSION['basket'][$_POST['id']]))
            {
                $product = get_product($_POST['id']);
        
                $_SESSION['basket'][$_POST['id']] = $product;
            }
    
            $_SESSION['basket'][$_POST['id']]['counter'] = $_POST['counter'];
        }
        else
        {
            unset($_SESSION['basket'][$_POST['id']]);
        }

        $_POST = [];
        $this_page = $_SERVER['REQUEST_URI'];
        header("Location: $this_page");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <!-- База -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Иконка с названием -->
    <link rel="shortcut icon" href="images/icons/company_icon.png">
    <title>Меню сети предприятий «Вкусно - и точка!»</title>

    <!-- Скрипты -->
    <script defer src="scripts/element_slider.js"></script>

    <!-- Стили страницы -->
    <link rel='stylesheet' type='text/css' href='fonts/sansation/stylesheet.css'>
    <link rel='stylesheet' type='text/css' href='styles/reset_style.css'>
    <link rel='stylesheet' type='text/css' href='styles/basic.css'>
    <link rel='stylesheet' type='text/css' href='styles/index.css'>
    <link rel='stylesheet' type='text/css' href='styles/element_slider.css'>
    <link rel='stylesheet' type='text/css' href='styles/card.css'>
</head>

<body <?= (!empty($_GET['id'])) ? "style='overflow-y: hidden'" : ""?>>
    <? require_once('header.php') ?>

    <main style="margin-top: 90px">

        <div class="gradient-line"></div> <!-- Линия с градиентом -->

        <!-- Блок приветствия -->
        <div class="welcome-block">
            <div class="container">

                <!-- Блок слайдера -->
                <div class="flex">

                    <!-- Слайдер реклама -->
                    <div class='slideshow-container'>
        
                        <!-- Фреймы слайдера -->
                        <div class="mySlides fade">
                            <img src="images/advertising/1.jpg" class='advertising_slide'>
                        </div>
        
                        <div class="mySlides fade">
                            <img src="images/advertising/2.jpg" class='advertising_slide'>
                        </div>
        
                        <div class="mySlides fade">
                            <img src="images/advertising/3.jpg" class='advertising_slide'>
                        </div>

                        <div class="mySlides fade">
                            <img src="images/advertising/4.jpg" class='advertising_slide'>
                        </div>

                        <div class="mySlides fade">
                            <img src="images/advertising/5.jpg" class='advertising_slide'>
                        </div>
        
                        <!-- Стрелки -->
                        <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
                        <a class="next" onclick="plusSlides(1)">&#10095;</a>
        
                        <!-- Кол-во слайдеров -->
                        <div style="text-align:center">
                            <span class="dot" onclick="currentSlide(1)"></span>
                            <span class="dot" onclick="currentSlide(2)"></span>
                            <span class="dot" onclick="currentSlide(3)"></span>
                            <span class="dot" onclick="currentSlide(4)"></span>
                            <span class="dot" onclick="currentSlide(5)"></span>
                        </div>
                    </div> <!-- /.slideshow-container -->

                    <!-- Картошка фри с бургером -->
                    <a href="?category=snacks">
                        <img src="images/Frame_1.png" class="container-picture" alt="Картошка фри с бургером">
                    </a>
                    
                </div> <!-- /.flex -->
            </div> <!-- /.container -->
        </div> <!-- /.welcome-block -->

        <div class="gradient-line"></div> <!-- Линия с градиентом -->
    

        <!-- Меню категорий товара -->
        <div class="product-category-menu indentation" id="menu">
            <div class="container">
                <div class="product-category-location">
                    <a href="?category=news">Новинки</a>
                    <a href="?category=popular">Популярное</a>
                    <a href="?category=drinks">Напитки</a>
                    <a href="?category=burgers">Бургеры</a>
                    <a href="?category=salads-and-rolls">Салаты и роллы</a>
                    <a href="?category=snacks">Картофель и стартеры</a>
                    <a href="?category=desserts">Десерты</a>
                    <a href="?category=sauces">Соусы</a>
                </div> <!-- /.product-category-location -->
            </div> <!-- /.container -->
        </div> <!-- /.roduct-category-menu -->


        <!-- Карточки товара -->
        <div class="centering-cards">
            <div class="container">
                <div class="marking-cards">
                    
                    <?
                        if (isset($category))
                        {
                            $products = get_all_products_in_category($category);
                        }
                        else
                        {
                            $products = get_all_products_in_status($status);
                        }

                        foreach ($products as $elem):
                        $product_category = $categories[$elem['category_id']];
                    ?>
                    <!-- Карточка товара -->
                    <div class="card">
                        <form id="<?= $elem['id'] ?>" action="" method="POST" hidden>
                            <input type="hidden" name="id" value="<?= $elem['id'] ?>">
                        </form>
                        <div class="flex" style="width: 100%; justify-content: <?= ($elem['status'] != 0) ? "space-between" : "flex-end" ?>; align-items: center">
                            <? if ($elem['status'] == 1): ?>
                            <span class="stamp news">Новинка</span>
                            <? elseif ($elem['status'] == 2): ?>
                            <span class="stamp popular">Популярное</span>
                            <? endif ?>
                            <h2 class="price-card"><?= ceil($elem['price']) ?> ₽</h2>
                        </div>
                        <a href="?category=<?=$_GET['category']?>&id=<?=$elem['id']?>">
                            <img src="/images/<?= (!empty($elem['picture'])) ? "menu/" . $product_category . "/" . $elem['picture'] : "icons/not_found.png" ?>" class="product-image" alt="<?= $elem['name'] ?>">
                        </a>
                        <!-- Название товара -->
                        <a href="?category=<?=$_GET['category']?>&id=<?=$elem['id']?>"><h1 class="product-name-card"><?= $elem['name'] ?></h1></a>

                        <!-- Подробнее о товаре -->
                        <div> <a href="?category=<?=$_GET['category']?>&id=<?=$elem['id']?>" class="more-details">Подробнее</a> </div>
                        
                        <? if (!empty($_SESSION)): ?>
                        <!-- Кнопки -->
                        <div class="card-button-layout">

                            <?
                                $counter = 0;

                                if (isset($_SESSION['basket']))
                                {
                                    foreach ($_SESSION['basket'] as $key => $value)
                                    {
                                        if ($elem['id'] == $key)
                                        {
                                            $counter = $value['counter'];
                                        }
                                    }
                                }
                            ?>

                            <? if ($counter == 0): ?>

                            <!-- Счётчик -->
                            <div class="quantity-counter">
                                <input id="counter-product" form="<?= $elem['id'] ?>" name="counter" type="hidden" value="<?= $counter ?>">
                                <button id="reduce-product">-</button>
                                <span id="num-product"><?= $counter ?></span>
                                <button id="add-product">+</button>
                            </div>

                            <!-- Кнопка добавить -->
                            <input form="<?= $elem['id'] ?>" type="submit" class="add-to-cart" value="Добавить">

                            <? else: ?>

                            <!-- Счётчик -->
                            <div class="quantity-counter">
                                <button class="button-not-allowed">-</button>
                                <span><?= $counter ?></span>
                                <button class="button-not-allowed">+</button>
                            </div>
                            <button class="blocked-button" disabled>Добавлено</button>

                            <? endif ?>

                        </div> <!-- /.card-button-layout -->
                        <? endif ?>
                    </div> <!-- /.card -->
                    <? endforeach ?>
    
                </div> <!-- /.marking-cards-->
            </div> <!-- /.container -->
        </div> <!-- /.centering-cards -->
    </main>

    <? require_once('footer.php') ?>

    <script>
        function $(element) {
            elem = document.querySelector(element);
            return elem;
        }

        function $all(element) {
            elem = document.querySelectorAll(element);
            return elem;
        }

        if ($('#error') != null) {
            // Получить координаты модального окна оформления заказа и установить модальное окно ошибки на 100 пикселей выше первого.

            let coordsModal = $('.modal').getBoundingClientRect();
            let coordsError = $('#error').getBoundingClientRect();

            $('#error').style.top = (coordsModal.top - 15 - coordsError.height) + "px";

            // Убрать уведомление об ошибке.
    
            $('#error').addEventListener('click', function () {
                $('#error').style.opacity = 0;
                $('#error').style.cursor = "default";
                setTimeout(() => {
                    $('#error').remove();
                }, 1000);
            })
        }

        // Счётчик количества у товара.

        // Добавление.

        $all('#add-product').forEach(function(elem, key) {
            elem.addEventListener('click', function (e) {
                $all('#num-product').forEach(function(elem2, key2) {
                    if (key == key2) {
                        num = Number(elem2.textContent) + 1;
                        elem2.innerHTML = num;
                    }
                });
                $all('#counter-product').forEach(function(elem2, key2) {
                    if (key == key2) {
                        elem2.setAttribute('value', num);
                    } 
                });
            });
        })

        // Убавление.

        $all('#reduce-product').forEach(function(elem, key) {
            elem.addEventListener('click', function (e) {
                $all('#num-product').forEach(function(elem2, key2) {
                    if (key == key2) {
                        num = Number(elem2.textContent);
                        if (num > 0) {
                            num -= 1;
                        }
                        elem2.innerHTML = num;
                    }
                });
                $all('#counter-product').forEach(function(elem2, key2) {
                    if (key == key2) {
                        elem2.setAttribute('value', num);
                    } 
                });
            });
        })
    </script>
</body>