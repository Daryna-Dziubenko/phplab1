<?php
$x_value = "Наші послуги";
$y_value = "2025. Дарина Дзюбенко";

$text2 = "<p>Я надаю послуги з веброзробки та дизайну:</p>
          <ul>
            <li>Створення сайтів</li>
            <li>Розробка інтерфейсів</li>
            <li>Верстка адаптивних макетів</li>
            <li>Розробка дизайнів</li>
          </ul>";

$text4 = "<p>Чому обирають мене:</p>
          <ol>
            <li>Якість</li>
            <li>Сучасні технології</li>
            <li>Доступні ціни</li>
            <li>Швидкість виконання</li>
          </ol>";

$text5 = "<p>Приклад: я можу зробити форму для відправки повідомлень.</p>
          <form>
              <label>Ваш email:</label><br>
              <input type='email' placeholder='example@mail.com'><br><br>
              <label>Повідомлення:</label><br>
              <textarea rows='3'></textarea><br><br>
              <button type='submit'>Надіслати</button>
          </form>";

$text6 = "<p>Наші клієнти: стартапи, студенти, компанії з IT-сфери.</p>";

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
