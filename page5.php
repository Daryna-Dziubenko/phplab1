// page5.php
<?php

require 'db_config.php';

$php_start_total = microtime(true);

$page_name = basename($_SERVER['PHP_SELF']);
$block_ids = ['b1', 'x', 'b2', 'b4', 'b5', 'b6', 'y']; 

$default_content = [
    // Вміст сторінки 5
    'b1' => 'Ласкаво прошу на мою першу лабораторну з PHP!',
    'x'  => 'Контакти',
    'b2' => "<p>Зв’яжіться з нами:</p>
            <p>Email: darinadarmidontova@gmail.com</p>
            <p>Телефон: +380 95 473 68 53</p>",
    'b4' => "<p>Соцмережі:</p>
            <ul>
                <li><a href='https://www.instagram.com/daryna_dz?igsh=bWg4NnUzZnY0aXlv' target='_blank'>Instagram: @daryna_dz</a></li>
                <li><a href='https://t.me/daryna_dz' target='_blank'>Telegram: @daryna_dz</a></li>
            </ul>",
    'b5' => "<p>Мапа розташування:</p>
            <iframe src='https://maps.google.com/maps?q=Kyiv&t=&z=13&ie=UTF8&iwloc=&output=embed' width='100%' height='120'></iframe>",
    'b6' => "<p>Залиште повідомлення:</p>
            <form>
                <label>Ім’я:</label><br>
                <input type='text'><br><br>
                <label>Ваше повідомлення:</label><br>
                <textarea rows='3'></textarea><br><br>
                <button type='submit'>Надіслати</button>
            </form>",
    'y' => '2025. Дарина Дзюбенко'
];

$db_content = [];
$db_start_time = microtime(true); 

try {
    $in_placeholders = implode(',', array_fill(0, count($block_ids), '?'));
  
    $sql = "SELECT [block_id], [content] FROM [page_content] WHERE [page_name] = ? AND [block_id] IN ($in_placeholders)";

    $stmt = $pdo->prepare($sql);
    $params = array_merge([$page_name], $block_ids);
    $stmt->execute($params);

    while ($row = $stmt->fetch()) {
        $db_content[$row['block_id']] = $row['content'];
    }

} catch (\PDOException $e) {
    error_log($e->getMessage());
}

$db_time_ms = round((microtime(true) - $db_start_time) * 1000, 2); 

function buildHtmlFromJson($json_string) {
    $data = json_decode($json_string, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return 'Помилка читання даних';
    }
   
    $html = '';
    if (!empty($data['text'])) $html .= '<div>' . nl2br(htmlspecialchars($data['text'])) . '</div>';
   
        if (!empty($data['list']) && is_array($data['list'])) {
        $html .= '<ul>';
        foreach ($data['list'] as $item) {
        $html .= '<li>' . htmlspecialchars($item) . '</li>';
    }
        $html .= '</ul>';
    }
     
    if (!empty($data['photo'])) $html .= '<img src="' . htmlspecialchars($data['photo']) . '" style="max-width:100%;max-height:200px;">';

    return $html ?: ' '; 
}

$page_data = [];
foreach ($block_ids as $id) {
    if (isset($db_content[$id])) {
        $page_data[$id] = buildHtmlFromJson($db_content[$id]);
    } else {
        $page_data[$id] = $default_content[$id];
    }
}

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

$php_total_time_ms = round((microtime(true) - $php_start_total) * 1000, 2);
$php_gen_time_ms = round($php_total_time_ms - $db_time_ms, 2);

?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title><?php echo strip_tags($page_data['x']); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body 
    data-php-gen-time="<?php echo $php_gen_time_ms; ?>" 
    data-db-time="<?php echo $db_time_ms; ?>"
    data-page-name="<?php echo $page_name; ?>">
     
    <div class="container">
        <div class="block b1">
            <?php echo $page_data['b1']; ?>
        </div>
        <div class="block x"><?php echo $page_data['x']; ?></div>
        <div class="block b2"><?php echo $page_data['b2']; ?></div>

        <div class="block b3 menu">
            <h3>Меню</h3>
            <ul>
                <?php foreach ($menu as $link => $name) {
                    $activeClass = isActivePage($link) ? 'active' : '';
                    echo "<li><a href='$link' class='$activeClass'>$name</a></li>";
                } ?>
            </ul>
        </div>

        <div class="block b4"><?php echo $page_data['b4']; ?></div>
        <div class="block b5"><?php echo $page_data['b5']; ?></div>
        <div class="block b6">
            <?php echo $page_data['b6']; ?>
            <div class="block y"><?php echo $page_data['y']; ?></div>
        </div>
    </div>
    <script src="lab.js"></script>
</body>
</html>