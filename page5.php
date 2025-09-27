<?php
$x_value = "Контакти";
$y_value = "2025. Дарина Дзюбенко";

$text2 = "<p>Зв’яжіться з нами:</p>
          <p>Email: darinadarmidontova@gmail.com</p>
          <p>Телефон: +380 95 473 68 53</p>";

$text4 = "<p>Соцмережі:</p>
          <ul>
            <li><a href='https://www.instagram.com/daryna_dz?igsh=bWg4NnUzZnY0aXlv' target='_blank'>Instagram: @daryna_dz</a></li>
            <li><a href='https://t.me/daryna_dz' target='_blank'>Telegram: @daryna_dz</a></li>
          </ul>";

$text5 = "<p>Мапа розташування:</p>
          <iframe src='https://maps.google.com/maps?q=Kyiv&t=&z=13&ie=UTF8&iwloc=&output=embed' width='100%' height='120'></iframe>";

$text6 = "<p>Залиште повідомлення:</p>
          <form>
              <label>Ім’я:</label><br>
              <input type='text'><br><br>
              <label>Ваше повідомлення:</label><br>
              <textarea rows='3'></textarea><br><br>
              <button type='submit'>Надіслати</button>
          </form>";

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
