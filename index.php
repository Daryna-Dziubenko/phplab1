<?php
require 'db_config.php'; // Підключення до бд

$php_start_total = microtime(true); // Початок відліку часу генерації

$page_name = basename($_SERVER['PHP_SELF']);
$block_ids = ['b1', 'x', 'b2', 'b4', 'b5', 'b6', 'y']; 
// Дефолтний контент (якщо база даних пуста)
$default_content = [
    'b1' => 'Ласкаво прошу на мою першу лабораторну з PHP!',
    'x'  => 'Мій перший PHP сайт',
    'b2' => "<p>Ласкаво прошу на головну сторінку!</p>
             <p>Тут ви знайдете загальну інформацію про мій сайт.</p>",
    'b4' => "<p>Останні новини від мене, Даші:</p>
             <ul>
               <li>Я почала працювати над власним проєктом-портфоліо.</li>
               <li>Вдосконалила навички у CSS та адаптивній верстці.</li>
               <li>Вивчила нові фішки PHP та JavaScript для інтерактивних сайтів.</li>
             </ul>",
    'b5' => "<p>Приклад мого коду на PHP:</p>
             <pre>
&lt;?php
// Виводимо привітання
echo 'Привіт! Мене звати Даша.';
?&gt;
             </pre>",
    'b6' => "<p>Рада познайомитися з вами:</p>
             <div class='image-container'>
               <img src='images/i.jpg' width='100' height='133'>
             </div>",
    'y'  => '2025. Дарина Дзюбенко'
];

$db_content = [];
$db_start_time = microtime(true); // Засікаємо час запиту до БД

try {
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

function buildHtmlFromJson($json_string) {
    if (empty($json_string)) return ''; // Якщо пусто - повертаємо нічого

    $data = json_decode($json_string, true);

    // Якщо це не JSON (старі дані з лаб 1-3), просто повертаємо текст як є
    if (json_last_error() !== JSON_ERROR_NONE) {
        return $json_string;
    }
    
    // А якщо це JSON, то формуємо HTML
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
    
    // Якщо це складний об'єкт Collapse (масив масивів), повертаємо нічого, 

    if (is_array($data) && isset($data[0]['title'])) {
        return ''; 
    }
    
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
    "page5.php" => "Контакти",
    "lab5.php"  => "Лаба 5"
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
    <title>Лаб 4 - Редактор (Page 1)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body data-php-gen-time="<?php echo $php_gen_time_ms; ?>">
    <div class="container">
        <div class="block b1"><?php echo $page_data['b1']; ?></div>
        <div class="block x">Page 1</div>

        <div class="block b2">
            <?php echo $page_data['b2']; ?>
            <hr>
            <a href="page2.php" class="btn btn-nav">Перейти до перегляду (Page 2) →</a>
        </div>
        
        <div class="block b3 menu">
             <h3>Меню</h3>
             <ul>
                <?php foreach ($menu as $link => $name) {
                    $activeClass = isActivePage($link) ? 'active' : '';
                    echo "<li><a href='$link' class='$activeClass'>$name</a></li>";
                } ?>
             </ul>
        </div>
        
        <div class="block b4">
            <?php echo $page_data['b4']; ?>
            
            <hr style="border-top: 2px dashed #bbb; margin: 20px 0;">
            
            <h3>Конструктор Collapse (JS)</h3>
            <p><small>Додайте блоки, які будуть розгортатися на сторінці 2:</small></p>
            
            <div id="builder-container"></div>

            <div style="margin-top: 15px;">
                <button class="btn btn-add" id="btn-add-row">+ Додати секцію</button>
            </div>
            <hr>
            <div>
                <button class="btn btn-save" id="btn-save-server">Зберегти на сервер</button>
                <span id="status-msg" style="margin-left: 10px; font-weight: bold;"></span>
            </div>
        </div>

        <div class="block b5"><?php echo $page_data['b5']; ?></div>
        
        <div class="block b6">
            <?php echo $page_data['b6']; ?>
            <div class="block y"><?php echo $page_data['y']; ?></div>
        </div>
    </div>

    <script src="lab.js?v=<?php echo time(); ?>"></script>
</body>
</html>