<?php
require 'db_config.php';

$php_start_total = microtime(true);
$page_name = basename($_SERVER['PHP_SELF']);
$block_ids = ['b1', 'x', 'b2', 'b4', 'b5', 'b6', 'y']; 

// Твій оригінальний контент
$default_content = [
    'b1' => 'Ласкаво прошу на мою першу лабораторну з PHP!',
    'x'  => 'Наші послуги',
    'b2' => "<p>Я надаю послуги з веброзробки та дизайну:</p>
             <ul>
               <li>Створення сайтів</li>
               <li>Розробка інтерфейсів</li>
               <li>Верстка адаптивних макетів</li>
               <li>Розробка дизайнів</li>
             </ul>",
    'b4' => "<p>Чому обирають мене:</p>
             <ol>
               <li>Якість</li>
               <li>Сучасні технології</li>
               <li>Доступні ціни</li>
               <li>Швидкість виконання</li>
             </ol>",
    'b5' => "<p>Приклад: я можу зробити форму для відправки повідомлень.</p>
             <form>
               <label>Ваш email:</label><br>
               <input type='email' placeholder='example@mail.com'><br><br>
               <label>Повідомлення:</label><br>
               <textarea rows='3'></textarea><br><br>
               <button type='submit'>Надіслати</button>
             </form>",
    'b6' => "<p>Наші клієнти: стартапи, студенти, компанії з IT-сфери.</p>",
    'y'  => '2025. Дарина Дзюбенко'
];

$db_content = [];
$db_start_time = microtime(true); 

try {
    // ВИПРАВЛЕНО: Прибрано квадратні дужки []
    $in_placeholders = implode(',', array_fill(0, count($block_ids), '?'));
    $sql = "SELECT block_id, content FROM page_content WHERE page_name = ? AND block_id IN ($in_placeholders)";
    
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

// Покращена функція: якщо це не JSON, повертає просто текст (важливо для редагування)
function buildHtmlFromJson($json_string) {
    $data = json_decode($json_string, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return $json_string; // Повертаємо як є, якщо це звичайний текст
    }
    
    // Якщо це складний JSON з Collapse (масив), не показуємо його тут
    if (is_array($data) && isset($data[0]['title'])) return $json_string;

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
    
    return $html ?: $json_string; 
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
    <script src="lab.js?v=<?php echo time(); ?>"></script>
</body>
</html>