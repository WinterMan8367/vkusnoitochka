<?
    $page = $_SERVER['REQUEST_URI'];
    $categories = [1 => 'burgers', 'drinks', 'snacks', 'desserts', 'sauces', 'salads-and-rolls'];

    if (!empty($_GET['delete']))
    {
        if ($_GET['delete'] == 'all')
        {
            $_SESSION['basket'] = [];
            $search = "&delete=all";
        }
        else
        {
            unset($_SESSION['basket'][$_GET['delete']]);
            $search = "&delete=" . $_GET['delete'];
        }

        $page = str_replace($search, "", $page);
        header("Location: $page");
        exit;
    }

    if (!empty($_GET['add_counter']))
    {
        $_SESSION['basket'][$_GET['add_counter']]['counter'] += 1;
        $page = str_replace("&add_counter=" . $_GET['add_counter'], "", $page);
        header("Location: $page");
        exit;
    }

    if (!empty($_GET['reduce_counter']))
    {
        if ($_SESSION['basket'][$_GET['reduce_counter']]['counter'] > 1)
        {
            $_SESSION['basket'][$_GET['reduce_counter']]['counter'] -= 1;
        }

        $page = str_replace("&reduce_counter=" . $_GET['reduce_counter'], "", $page);
        header("Location: $page");
        exit;
    }

    if (isset($_POST['address']))
    {
        $order = [];

        if (empty($_POST['address']))
        {
            $error[] = "Вы не указали адрес.";
        }

        if (isset($_POST['send_bonus']))
        {
            if ($_POST['bonus'] < 0)
            {
                $error[] = "Вы указали отрицательное количество бонусов.";
            }
    
            if ($_POST['bonus'] > $_SESSION['user']['point'])
            {
                $error[] = "Количество бонусов, которое вы хотите списать, больше того, сколько их у вас всего.";
            }

            if (empty($error))
            {
                $all_price = 0;
                foreach ($_SESSION['basket'] as $product)
                {
                    $all_price += $product['price'] * $product['counter'];
                }

                if ($_POST['bonus'] > $all_price)
                {
                    $error[] = "Количество бонусов, которое вы хотите списать, больше стоимости всей покупки.";
                    $error[] = "Ориентировочная стоимость - $all_price ₽; вы хотите списать - $_POST[bonus] ₽";
                }
                else
                {
                    $order['send_bonus'] = $_POST['bonus'];
                }
            }
        }

        if (isset($_POST['send_coupon']))
        {
            $send_coupon = get_coupon_by_id($_POST['coupon']);
            foreach ($_SESSION['basket'] as $elem)
            {
                if ($send_coupon['product_id'] == $elem['id'])
                {
                    $discount_price = round($elem['price'] * ($send_coupon['discount'] / 100), 2);
                }
            }
            $order['coupon']['id'] = $_POST['coupon'];
            $order['coupon']['discount_price'] = $discount_price;
        }

        if (empty($error))
        {
            $order['address'] = $_POST['address'];

            $all_price = 0;
            $all_products = 0;

            foreach ($_SESSION['basket'] as $product)
            {
                $all_products += 1;
                $all_price += $product['price'] * $product['counter'];
            }

            if (!isset($order['send_bonus'])) $order['save_bonus'] = round($all_price / 100 * 5, 2);

            $order['all_price'] = $all_price;
            $order['all_products'] = $all_products;

            $_POST = [];
            $_SESSION['order'] = $order;
            header("Location: $_SERVER[SCRIPT_NAME]");
            exit;
        }
    }

    if (isset($_POST['complete_order']))
    {
        $order = $_SESSION['order'];
        if ($_POST['complete_order'] == "yes")
        {
            if (isset($order['send_bonus']))
            {
                $points = $_SESSION['user']['point'] - $order['send_bonus'];
                $_SESSION['user']['point'] = $points;
                edit_point($_SESSION['user']['id'], $points);
            }

            if (isset($order['save_bonus']))
            {
                $points = round($_SESSION['user']['point'] + $order['save_bonus'], 2);
                $_SESSION['user']['point'] = $points;
                edit_point($_SESSION['user']['id'], $points);
            }

            if (isset($order['coupon']))
            {
                delete_coupon($order['coupon']['id']);
            }

            if ($order['all_price'] >= 1000)
            {
                $_SESSION['information']['random_coupon'] = generate_random_coupon($_SESSION['user']['id']);
            }

            unset($_SESSION['basket']);
            unset($_SESSION['order']);
            $_POST = [];
            $_SESSION['information']['modal'] = true;
            header("Location: $_SERVER[SCRIPT_NAME]");
            exit;
        }
        elseif ($_POST['complete_order'] == "no")
        {
            unset($_SESSION['order']);
            $_POST = [];
            header("Location: $_SERVER[SCRIPT_NAME]");
            exit;
        }
        else
        {
            unset($_SESSION['order']);
            $_POST = [];
            echo "Вы дошли до конца internet...";
            exit;
        }
    }
?>

<!-- Стили -->
<link rel='stylesheet' type='text/css' href='styles/header.css'>

<!-- Шапка страницы -->
<header id="header">
    <div class="container">
        <div class="header-menu-location">

            <!-- Логотип -->
            <div>
                <a href="index.php" class="header-logo"><img src="images/logos/full_logo.png" alt="Вкусно - и точка!"></a>
            </div>

            <!-- Меню сайта -->
            <div class="header-menu">
                <a href="index.php#menu">Меню</a>
                <a href="promotions.php">Акции</a>
                <a href="cafe.php">Кафе</a>
                <a href="quality.php">Качество</a>
                <a href="application.php">Приложение</a>
                <a href="about-us.php" style="margin: 0px;">О нас</a>
            </div>
                    
            <!-- Вход -->
            <div class="flex">
                <?
                    $href = "href='$_SERVER[REQUEST_URI]?basket'";
                    $class = "";

                    if (!empty($_GET))
                    {
                        $href = "href='$_SERVER[REQUEST_URI]&basket'";
                    }

                    if (empty($_SESSION['basket']))
                    {
                        $href = "";
                        $class = "basket-not-allowed";
                    }
                ?>
                <? if (!empty($_SESSION)): ?>
                    <div class="header-menu-basket <?= $class ?>">
                        <a <?= $href ?>>
                            <img src="images/icons/icon-food-basket.png" alt="Корзина">
                        </a>
                    </div>
                <? endif ?>
                <div class="header-menu-authorization">
                    <a href="<?= (!empty($_SESSION['user'])) ? "personal-profile.php" : "login.php" ?>"> <?= (!empty($_SESSION['user'])) ? $_SESSION['user']['name'] : "Вход" ?>
                        <img src="images/logos/hamburger_logo.png" id="hamburger_logo" alt="Бургер">
                    </a>
                </div>
            </div>
            
        </div> <!-- /.header-menu-location -->
    </div> <!-- /.container -->
</header> <!-- /.header -->

<? if (isset($_GET['basket']) or !empty($_GET['id']) or isset($_GET['checkout']) or isset($_SESSION['order']) or isset($_SESSION['information'])): ?>
    <link rel='stylesheet' type='text/css' href='styles/basket.css'>
    <style>
        body { overflow-y: hidden }
    </style>
    <div id="dark-blur-basket"></div>
<? endif ?>

<?
if (!empty($_GET['id'])):
    $product = get_product($_GET['id']);
    $product_category = $categories[$product['category_id']];
?>

    <!-- Подробная карточка товара -->
    <link rel='stylesheet' type='text/css' href='styles/card.css'>    
    <div id="card">
        <div class="head">
            <? if ($product['status'] == 1): ?>
                <div class="status font-low bold news">Новинка</div>
            <? elseif ($product['status'] == 2): ?>
                <div class="status font-low bold popular">Популярное</div>
            <? endif ?>
            <img class="image" src="/images/<?= (!empty($product['picture'])) ? "menu/" . $product_category . "/" . $product['picture'] : "icons/not_found.png" ?>">
            <div>
                <div class="name font-high bold"><?= $product['name'] ?></div>
                <div class="font-low orange bold"><?= ceil($product['price']) ?> &#8381; + <?= $product['price'] / 100 * 5 ?> <span class="ruble"></span> на счёт</div>
            </div>
            <?
                $search = ["?id=$_GET[id]", "&id=$_GET[id]"];
                $close_link = str_replace($search, "", $_SERVER['REQUEST_URI']);
            ?>
            <a href="<?= $close_link ?>" class="close"></a>
        </div>
        <div class="orange-line"></div>
        <div class="description">
            <h1 class="font-medium bold orange">Описание</h1>
            <p class="font-low"><?= $product['description'] ?></p>
        </div>
        <div class="orange-line"></div>
        <div class="nutritional">
            <h1 class="font-medium bold orange">Пищевая ценность (в порции)</h1>
            <table>
                <tr>
                    <td>
                        Вес:
                    </td>
                    <td>
                        <?= ceil($product['weight']) ?> г
                    </td>
                </tr>
                <tr>
                    <td>
                        Энергетическая ценность:
                    </td>
                    <td>
                        <?= ceil($product['ccal']) ?> Ккал / <?= ceil($product['ccal'] * 4.1868) ?> кДж
                    </td>
                </tr>
                <tr>
                    <td>
                        Белки:
                    </td>
                    <td>
                        <?= ceil($product['proteins']) ?> г
                    </td>
                </tr>
                <tr>
                    <td>
                        Жиры:
                    </td>
                    <td>
                        <?= ceil($product['fats']) ?> г
                    </td>
                </tr>
                <tr>
                    <td>
                        Углеводы:
                    </td>
                    <td>
                        <?= ceil($product['carbohydrates']) ?> г
                    </td>
                </tr>
            </table>
        </div>
        <? if (!empty($product['ingredient'])): ?>
            <div class="orange-line"></div>
            <div class="compound">
                <h1 class="font-medium bold orange">Состав</h1>
                <? foreach ($product['ingredient'] as $elem): ?>
                    <details>
                        <summary class="font-low">
                            <span class="compound-name"><?=$elem['name']?></span>
                            <span class="arrow"></span>
                        </summary>
                        <p class="font-low">
                            <?=$elem['compound']?>
                        </p>
                    </details>
                <? endforeach ?>
            </div>
        <? endif ?>
    </div> <!-- /#card -->

<? endif ?>

<? if (isset($_GET['basket'])): ?>

    <?
    $search = ["?basket", "&basket"];

    if (isset($_GET['id']))
    {
        $search[] = "?id=$_GET[id]";
        $search[] = "&id=$_GET[id]";
    }

    $this_page = str_replace($search, "", $_SERVER['REQUEST_URI']);
    ?>

    <div id="basket">

        <a href="<?= $this_page ?>" class="close-basket"></a>

        <div class="orange-line"></div> <!-- Линия с градиентом -->

        <!-- Заголовок корзины -->
        <div class="basket-title">

            <h1>Корзина</h1>

            <!-- Кнопка отчистки -->
            <a href="<?= $_SERVER['REQUEST_URI'] ?>&delete=all">
                <img src="images\icons\icon-basket.png" alt="Мусорка">
                <span>Убрать всё</span>
            </a>

        </div> <!-- /.basket-title -->

        <div class="orange-line"></div> <!-- Линия с градиентом -->

        <div class="omg">

            <?= (empty($_SESSION['basket'])) ? "<p style='padding: 10px; text-align: center'>Пока что ваша корзина пуста. :( <br> Но вы можете добавить в неё товар!</p>" : "" ?>

            <?
                foreach ($_SESSION['basket'] as $product):
                $category_basket = $categories[$product['category_id']];
            ?>
            <!-- Карточка с продуктом -->
            <div class="product-card-in-cart">
                    
                <!-- Фото продукта -->
                <a href="<?= "$_SERVER[REQUEST_URI]&id=$product[id]" ?>" class="flex">
                    <img src="images\menu\<?= $category_basket ?>\<?= $product['picture'] ?>" class="photo-product-in-cart" alt="<?= $product['name'] ?>">
                </a>
                    
                <!-- Разметка [Название товара] и [Кнопка отчистки] -->
                <div class="product-in-cart-mockup-1">

                    <!-- Разметка №1 -->
                    <div class="product-in-cart-mockup-2">

                        <!-- Название товара -->
                        <h1><?= $product['name'] ?></h1>
        
                        <!-- Кнопка отчистки -->
                        <a href="<?= $_SERVER['REQUEST_URI'] ?>&delete=<?= $product['id'] ?>"><img src="images\icons\icon-basket.png" alt="Мусорка"></a>
        
                    </div> <!-- /.product-in-cart-mockup-2 -->
                        
                    <!-- Разметка №2 -->
                    <div class="product-in-cart-mockup-2">
        
                        <!-- Счётчик -->
                        <div class="product-counrte">
                            <a <?= ($product['counter'] != 1) ? "href='$page&reduce_counter=$product[id]'" : "" ?> <?= ($product['counter'] == 1) ? "class='button-not-allowed'" : "" ?>>-</a>
                            <span><?= $product['counter'] ?></span>
                            <a href="<?= $page . "&add_counter=" . $product['id'] ?>">+</a>
                        </div>
                            
                        <!-- Цена -->
                        <span><?= ceil($product['price']) ?> ₽</span>
        
                    </div> <!-- /.product-in-cart-mockup-2 -->
        
                </div> <!-- /.product-in-cart-mockup-1 -->
            </div> <!-- /.product-card-in-cart -->
            <? endforeach ?>
        </div> <!-- /.omg -->

        <? if (!empty($_SESSION['basket'])): ?>
            
        <div class="orange-line"></div> <!-- Линия с градиентом -->

        <?
            $all_price = 0;
            $all_products = 0;

            foreach ($_SESSION['basket'] as $product)
            {
                $all_products += 1;
                $all_price += $product['price'] * $product['counter'];
            }
        ?>

        <div class="order-summary">
            <div> <!-- Стоимость доставки -->
                <p>Стоимость доставки:</p>
                <span><?= ($all_price < 1000) ? 200 : 0 ?> ₽</span>
            </div>
                
            <div> <!-- Сумма -->
                <p><?= $all_products ?> товара на сумму:</p>
                <span><?= ceil($all_price) ?> ₽</span>
            </div>

            <div> <!-- Бонусы -->
                <p>Бонусов за заказ:</p>
                <span class="align">
                    <?= $all_price / 100 * 5 ?>
                    <span class="ruble"></span>
                </span>
            </div>

            <?
                $search = ["?basket", "&basket"];

                $this_page = str_replace($search, "", $_SERVER['REQUEST_URI']);

                unset($_GET['basket']);

                $checkout = (empty($_GET)) ? "?checkout" : "&checkout"
            ?>

            <div> <!-- Кнопка -->
                <a href="<?= $this_page . $checkout ?>">Оформить заказ</a>
            </div>
        </div> <!-- /.order-summary -->

        <div class="orange-line"></div> <!-- Линия с градиентом -->

        <? endif ?>
            
    </div> <!-- /.basket -->

<? endif ?>

<? if (isset($_GET['checkout'])): ?>

    <?
    $search = ["?checkout", "&checkout"];

    $this_page = str_replace($search, "", $_SERVER['REQUEST_URI']);
    ?>

    <? if (isset($error)): ?>
        <div id="error">
            <span class="error-note">Кликните, чтобы убрать ошибку.</span>
            <?
            foreach ($error as $elem)
            {
                echo $elem . "<br>";
            }
            ?>
        </div>
    <? endif ?>

    <div class="modal">
        <a href="<?= $this_page ?>" class="close-checkout"></a>
        <h1>Ваш заказ оформлен!</h1>
        <h1>Осталось уточнить некоторые данные</h1>
        <form action="" method="POST">
            <input type="hidden" value="1">
            <p>Укажите адрес доставки:</p>
            <input type="text" name="address">
            <? if ($_SESSION['user']['point'] > 0): ?>
                <p>Списываем бонусы или копим?</p>
                <label>
                    <input type="checkbox" name="send_bonus">
                    <span>Копим</span>
                </label>
                <p class="bonus disabled">Сколько списываем бонусов?</p>
                <input class="bonus disabled" type="number" name="bonus">
            <? endif ?>
            <?
            $coupons_by_items_in_basket = get_coupons_by_items_in_basket($_SESSION['user']['id'], $_SESSION['basket']);
            if (!empty($coupons_by_items_in_basket)):
            ?>
                <p>Используем купон?</p>
                <label>
                    <input type="checkbox" name="send_coupon">
                    <span>Не используем</span>
                </label>
                <p class="use-coupon disabled">Выберите купон, который хотите использовать:</p>
                <select class="use-coupon disabled" name="coupon">
                    <?
                    foreach ($coupons_by_items_in_basket as $elem):
                        $product = get_product($elem['product_id']);
                    ?>
                        <option value="<?= $elem['id'] ?>">Купон на покупку <?= $product['name'] ?> со скидкой -<?= $elem['discount'] ?>%</option>
                    <? endforeach ?>
                </select>
            <? endif ?>
            <div class="block-submit">
                <input type="submit">
            </div>
        </form>
    </div>

    <script>
        function $(element) {
            elem = document.querySelector(element);
            return elem;
        }

        function $all(element) {
            elem = document.querySelectorAll(element);
            return elem;
        }

        let coordsModalo = $('.modal').getBoundingClientRect();
        $('.modal').style.top = "calc(50% - " + (coordsModalo.height / 2) + "px)";
        $('.modal').style.left = "calc(50% - " + (coordsModalo.width / 2) + "px)";
        
        if ($('#error') != null && $('.modal') != null) {

            let coordsModalo = $('.modal').getBoundingClientRect();
            let coordsError = $('#error').getBoundingClientRect();

            $('#error').style.top = (coordsModalo.top - 15 - coordsError.height) + "px";
    
            $('#error').addEventListener('click', function () {
                $('#error').style.opacity = 0;
                $('#error').style.cursor = "default";
                setTimeout(() => {
                    $('#error').remove();
                }, 1000);
            })
        }

        if ($('input[name="send_bonus"]') != null)
        {
            $('input[name="send_bonus"]').addEventListener('change', function(event) {
                if (event.target.checked)
                {
                    $('input[name="send_bonus"] + span').textContent = "Списываем";
    
                    $all('.bonus.disabled').forEach(function(elem, key) {
                        elem.classList.remove("disabled");
                        elem.classList.add("enabled");
                    });
                }
                else
                {
                    $('input[name="send_bonus"] + span').textContent = "Копим";
    
                    $all('.bonus.enabled').forEach(function(elem, key) {
                        elem.classList.remove("enabled");
                        elem.classList.add("disabled");
                    });
                }
            });
        }

        if ($('input[name="send_coupon"]') != null)
        {
            $('input[name="send_coupon"]').addEventListener('change', function(event) {
                if (event.target.checked)
                {
                    $('input[name="send_coupon"] + span').textContent = "Используем";
    
                    $all('.use-coupon.disabled').forEach(function(elem, key) {
                        elem.classList.remove("disabled");
                        elem.classList.add("enabled");
                    });
                }
                else
                {
                    $('input[name="send_coupon"] + span').textContent = "Не используем";
    
                    $all('.use-coupon.enabled').forEach(function(elem, key) {
                        elem.classList.remove("enabled");
                        elem.classList.add("disabled");
                    });
                }
            });
        }

    </script>

<? endif ?>

<?
if (!empty($_SESSION['order'])):
    $order = $_SESSION['order'];
    $bonus_count = (isset($order['send_bonus'])) ? -$order['send_bonus'] : $order['save_bonus'];
    $coupon_discount = (isset($order['coupon'])) ? $order['coupon']['discount_price'] : 0;
    $delivery = ($order['all_price'] < 1000) ? 200 : 0;
    $total_price = $order['all_price'] + $delivery - $coupon_discount;
    if ($bonus_count < 0) $total_price += $bonus_count;
?>
    <div class="complete-order">
        <h1>Завершение оформления заказа</h1>
        <table>
            <tr>
                <td>Доставка по адресу:</td>
                <td class="bold"><?= $order['address'] ?></td>
            </tr>
            <tr>
                <td>Всего <span class="bold"><?= $order['all_products'] ?></span> товар(а/ов) на сумму:</td>
                <td class="bold"><?= $order['all_price'] ?> ₽</td>
            </tr>
            <tr>
                <td>Стоимость доставки:</td>
                <td class="<?= ($delivery > 0) ? "red" : "orange"?> bold"><?= $delivery ?> ₽</td>
            </tr>
            <? if ($bonus_count > 0): ?>
                <tr>
                    <td>Вы получите:</td>
                    <td class="orange bold"><?= $bonus_count ?> <span class="ruble"></span></td>
                </tr>
            <? else: ?>
                <tr>
                    <td>Вы потратите:</td>
                    <td class="red bold"><?= -$bonus_count ?> <span class="ruble"></span></td>
                </tr>
            <? endif ?>
            <? if ($coupon_discount > 0): ?>
                <tr>
                    <td>Скидка по купону: </td>
                    <td class="orange bold"><?= $coupon_discount ?> ₽</td>
                </tr>
            <? endif ?>
        </table>
        <hr>
        <p class="bold high">Итого:</p>
        <table>
            <tr>
                <td>К оплате:</td>
                <td class="bold"><?= $total_price ?> ₽</td>
            </tr>
            <? if ($bonus_count > 0): ?>
                <tr>
                    <td>Будет начислено: </td>
                    <td class="orange bold"><?= $bonus_count ?> <span class="ruble"></span></td>
                </tr>
            <? endif ?>
        </table>
        <hr>
        <p class="bold high center">Оформляем заказ?</p>
        <div class="complete-buttons">
            <form action="" method="POST">
                <input type="hidden" name="complete_order" value="yes">
                <input type="submit" value="Да">
            </form>
            <form action="" method="POST">
                <input type="hidden" name="complete_order" value="no">
                <input type="submit" value="Нет">
            </form>
        </div>
    </div>
<? endif ?>

<? if (isset($_SESSION['information']['modal'])): ?>
    <div class="complete-order">
        <h1>Ваш заказ успешно оформлен!</h1>
        <p class="center">Спасибо, что выбрали нас!</p>
        <p class="center">Ожидайте, ваш заказ будет доставлен в течение 30 минут.</p>
        <?
        if (isset($_SESSION['information']['random_coupon'])):
            $random_coupon_product = get_product($_SESSION['information']['random_coupon']['product_id']);
        ?>
            <hr>
            <p class="center">Вы получили купон на покупку <span class="bold orange"><?= $random_coupon_product['name'] ?></span> со скидкой <span class="bold red">-<?= $_SESSION['information']['random_coupon']['discount'] ?>%</span></p>
        <? endif ?>
        <div class="complete-buttons">
            <a class="center" href="<?= $_SERVER['SCRIPT_NAME'] ?>">Понятно</a>
        </div>
    </div>
<?
unset($_SESSION['information']);
endif;
?>