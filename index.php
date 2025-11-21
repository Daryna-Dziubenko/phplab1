<?php
require 'db_config.php';

$php_start_total = microtime(true);

$page_name = basename($_SERVER['PHP_SELF']);
$block_ids = ['b1', 'x', 'b2', 'b4', 'b5', 'b6', 'y']; 

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
<?php
// Виводимо привітання
echo 'Привіт! Мене звати Даша.';
?>
             </pre>",
    'b6' => "<p>Рада познайомитися з вами:</p>
             <div class='image-container'>
               <img src='images/i.jpg' width='100' height='133'>
             </div>",
    'y'  => '2025. Дарина Дзюбенко'
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
    
    return $html ?: '&nbsp;'; 
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
    <title>Лаб 4 - Редактор (Page 1)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="block b1">Лабораторна робота №4. Варіант 2 (Collapse)</div>
        <div class="block x">Page 1</div>

        <div class="block b2">
            <p><strong>Панель адміністратора.</strong></p>
            <p>Додайте секції у центральному блоці, введіть заголовок та текст, потім натисніть "Зберегти".</p>
        </div>
        
        <div class="block b3 menu">
             <h3>Меню</h3>
             <ul>
                <li><a href="index.php" class="active">Редактор (Page 1)</a></li>
                <li><a href="page2.php">Перегляд (Page 2)</a></li>
             </ul>
        </div>
        
        <div class="block b4">
            <h3>Конструктор Collapse</h3>
            
            <div id="builder-container"></div>

            <div style="margin-top: 15px;">
                <button class="btn btn-add" id="btn-add-row">+ Додати секцію</button>
            </div>
            <hr>
            <div>
                <button class="btn btn-save" id="btn-save-server">Зберегти на сервер (AJAX)</button>
                <span id="status-msg" style="margin-left: 10px; font-weight: bold;"></span>
            </div>
        </div>

        <div class="block b5">Додаткова інформація...</div>
        
        <div class="block b6">
            <p>Футер сайту</p>
            <div class="block y">2025</div>
        </div>
    </div>

    <script src="lab.js"></script>
</body>
</html>