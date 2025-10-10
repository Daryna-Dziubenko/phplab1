<?php
$php_start = microtime(true);
$x_value = "Портфоліо";
$y_value = "2025. Дарина Дзюбенко";

$text2 = "<p>Мої приклади робіт:</p>
          <ul>
            <li>Бейджики</li>
            <li>Флаєри</li>
            <li>Мапи</li>
            <li>створення сайтів</li>
          </ul>";

$text4 = "<p>Фото прикладів:</p>
          <div class='image-container'>
              <img src='images/project1.png' width='50'>
              <img src='images/project2.png' width='150'>
              <img src='images/project3.jpg' width='160'>
          </div>";

$text5 = "<p>Я не стою на місці! Постійно працюю над новими проєктами та з радістю ділюся свіжими напрацюваннями. Зазирай частіше!</p>";

$text6 = "<p>Більше портфоліо можна знайти у нашому GitHub.</p>
          <a href='https://github.com/' target='_blank'>Переглянути GitHub</a>";

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
<body data-php-time="<?php echo round((microtime(true) - $php_start)*1000, 2); ?>">
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
    <script src="lab.js"></script>
</body>
</html>
