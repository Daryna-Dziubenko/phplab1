<?php
$x_value = "Мій перший PHP сайт";
$y_value = "2025. Дарина Дзюбенко";

$text2 = "<p>Ласкаво прошу на головну сторінку!</p>
          <p>Тут ви знайдете загальну інформацію про мій сайт.</p>";

$text4 = "<p>Останні новини від мене, Даші:</p>
          <ul>
            <li>Я почала працювати над власним проєктом-портфоліо.</li>
            <li>Вдосконалила навички у CSS та адаптивній верстці.</li>
            <li>Вивчила нові фішки PHP та JavaScript для інтерактивних сайтів.</li>
          </ul>";

$text5 = "<p>Приклад мого коду на PHP:</p>
          <pre>
&lt;?php
// Виводимо привітання
echo 'Привіт! Мене звати Даша.';
?&gt;
          </pre>";


$text6 = "<p>Рада познайомитися з вами:</p>
         <div class='image-container'>
           <img src='images/i.jpg' width='100' height='133'>
         </div>";

$menu = [
    "index.php" => "Головна",
    "page2.php" => "Про нас",
    "page3.php" => "Послуги",
    "page4.php" => "Портфоліо",
    "page5.php" => "Контакти"
];

function isActivePage($page) {
    $current_page = basename($_SERVER['PHP_SELF']);
    return ($current_page == $page) ? 'active' : '';
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
