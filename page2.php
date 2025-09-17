<?php
$x_value = "Про нас";
$y_value = "2025. Дарина Дзюбенко";

$text2 = "<p>Я, Даша, студентка ФІОТ КПІ, яка захоплюється дизайном та веброзробкою. Навчаюсь активно, проходила курси в Halel та інших онлайн-школах, щоб покращити свої навички у PHP, HTML, CSS і JavaScript. Люблю створювати сучасні, зручні та красиві сайти, експериментую з різними фреймворками та технологіями. Мрію працювати у великій IT-компанії, де зможу реалізовувати цікаві проєкти, розвивати свої навички та навчатися у досвідчених фахівців. Крім коду, цікавлюсь графічним дизайном і UI/UX, бо вважаю, що поєднання технічних та креативних здібностей робить веброзробку по-справжньому цікавою та захопливою.</p>";
$text4 = "<p>Мої досягнення:</p>
          <ul>
            <li>Створила багато сайтів які надихають вас щодня</li>
            <li>Опановую нові технології та інструменти</li>
            <li>Постійно вдосконалюю свої навички</li>
          </ul>";
$text5 = "<p>Вірю у силу наполегливості та власної креативності у навчанні та роботі.</p>";
$text6 = "<p>Обожнюю свою роботу та ділитися нею зі світом.</p>";

$menu = [
    "index.php" => "Головна",
    "page2.php" => "Про нас",
    "page3.php" => "Послуги",
    "page4.php" => "Портфоліо",
    "page5.php" => "Контакти"
];

function isActivePage($page) {
    return (basename($_SERVER['PHP_SELF']) == $page) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title><?php echo $x_value; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="block b1">
            Ласкаво прошу на мою першу лабораторну з PHP!
        </div>
        <div class="block x"><?php echo $x_value; ?></div>
        <div class="block b2"><?php echo $text2; ?></div>
        <div class="block b3 menu">
            <h3>Меню</h3>
            <ul>
                <?php foreach ($menu as $link => $name) {
                    $activeClass = isActivePage($link) ? 'active' : '';
                    echo "<li><a href='$link' class='$activeClass'>$name</a></li>";
                } ?>
            </ul>
        </div>
        <div class="block b4"><?php echo $text4; ?></div>
        <div class="block b5"><?php echo $text5; ?></div>
        <div class="block b6">
            <?php echo $text6; ?>
            <div class="block y"><?php echo $y_value; ?></div>
        </div>
    </div>
</body>
</html>
