<?php
require 'db_config.php';

$php_start_total = microtime(true);

$page_name = basename($_SERVER['PHP_SELF']);
$block_ids = ['b1', 'x', 'b2', 'b4', 'b5', 'b6', 'y']; 

$default_content = [
    'b1' => 'Ласкаво прошу на мою першу лабораторну з PHP!',
    'x'  => 'Про нас',
    'b2' => "<p>Я, Даша, студентка ФІОТ КПІ, яка захоплюється дизайном та веброзробкою. Навчаюсь активно, проходила курси в Halel та інших онлайн-школах, щоб покращити свої навички у PHP, HTML, CSS і JavaScript. Люблю створювати сучасні, зручні та красиві сайти, експериментую з різними фреймворками та технологіями. Мрію працювати у великій IT-компанії, де зможу реалізовувати цікаві проєкти, розвивати свої навички та навчатися у досвідчених фахівців. Крім коду, цікавлюсь графічним дизайном і UI/UX, бо вважаю, що поєднання технічних та креативних здібностей робить веброзробку по-справжньому цікавою та захопливою.</p>",
    'b4' => "<p>Мої досягнення:</p>
             <ul>
               <li>Створила багато сайтів які надихають вас щодня</li>
               <li>Опановую нові технології та інструменти</li>
               <li>Постійно вдосконалюю свої навички</li>
             </ul>",
    'b5' => "<p>Вірю у силу наполегливості та власної креативності у навчанні та роботі.</p>",
    'b6' => "<p>Обожнюю свою роботу та ділитися нею зі світом.</p>",
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

// 6. Функція buildHtmlFromJson
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
    <title>Лаб 4 - Перегляд (Page 2)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="block b1">Лабораторна робота №4. Клієнтська частина</div>
        <div class="block x">Page 2</div>

        <div class="block b2">
            <p><strong>Режим перегляду.</strong></p>
            <p>Ця сторінка автоматично синхронізується з сервером кожні 3 секунди.</p>
        </div>
        
        <div class="block b3 menu">
             <h3>Меню</h3>
             <ul>
                <li><a href="index.php">Редактор (Page 1)</a></li>
                <li><a href="page2.php" class="active">Перегляд (Page 2)</a></li>
             </ul>
        </div>
        
        <div class="block b4">
            <h3>Інтерактивний блок (Collapse)</h3>
            
            <div id="collapse-root" class="collapse-container">
                <em>Завантаження даних...</em>
            </div>

            <div style="margin-top:10px; font-size: 0.8em; color: gray;">
                Статус: <span id="last-update">Очікування...</span>
            </div>
        </div>

        <div class="block b5">Рекламний блок...</div>
        
        <div class="block b6">
            <p>Футер сайту</p>
            <div class="block y">2025. Дарина Дзюбенко</div>
        </div>
    </div>

    <script src="lab.js"></script>
</body>
</html>