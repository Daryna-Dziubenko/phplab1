<?php
require 'db_config.php';

// Замір часу 
$php_start_total = microtime(true);
$page_name = basename($_SERVER['PHP_SELF']);

// Масив меню 
$menu = [
    "index.php" => "Головна",
    "page2.php" => "Про нас",
    "page3.php" => "Послуги",
    "page4.php" => "Портфоліо",
    "page5.php" => "Контакти",
    "lab5.php"  => "Лаба 5 (HighLoad)"
];

$db_time_ms = 0; // Тут БД використовується лише через AJAX, тому 0
$php_total_time_ms = round((microtime(true) - $php_start_total) * 1000, 2);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Лабораторна №5 - High Load</title>
    <link rel="stylesheet" href="style.css">
    <style>
        
        /* Модальне вікно на весь екран */
        #work-overlay {
            display: none;
            position: fixed;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 90%; height: 90%;
            background: #fdfdfd;
            border: 3px solid #333;
            z-index: 2000;
            box-shadow: 0 0 50px rgba(0,0,0,0.7);
            border-radius: 8px;
        }
        
        /* Панель керування зверху */
        #controls-area {
            height: 60px;
            background: #eee;
            padding: 10px;
            border-bottom: 2px solid #ccc;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Область анімації */
        #anim-area {
            position: relative;
            width: 100%;
            height: calc(100% - 60px);
            background-color: #fff;
            background-image: radial-gradient(#ddd 1px, transparent 1px);
            background-size: 20px 20px;
            overflow: hidden;
        }

        /* Кульки з 3D ефектом */
        .ball {
            position: absolute;
            width: 20px; height: 20px;
            border-radius: 50%;
            transition: transform 0.1s;
        }
        #ball1 { background: radial-gradient(circle at 30% 30%, #ff5e5e, #a00000); }
        #ball2 { background: radial-gradient(circle at 30% 30%, #5eff5e, #008000); }

        /* Лог подій */
        #log-console {
            position: absolute;
            bottom: 10px; left: 10px;
            width: 250px; height: 150px;
            background: rgba(0,0,0,0.8);
            color: lime;
            font-family: monospace;
            font-size: 11px;
            overflow-y: auto;
            padding: 5px;
            border-radius: 4px;
            pointer-events: none; /* Щоб кліки проходили крізь нього */
        }

        /* Таблиця результатів */
        #results-table table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 13px;
        }
        #results-table th { background: #007bff; color: white; padding: 8px; }
        #results-table td { border: 1px solid #ddd; padding: 6px; text-align: center; }
        #results-table tr:nth-child(even) { background-color: #f2f2f2; }
        #results-table tr:hover { background-color: #ddd; }

        /* Кнопки */
        .btn-lab {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            color: white;
        }
        .btn-start { background: #28a745; }
        .btn-stop { background: #ffc107; color: black; }
        .btn-close { background: #dc3545; }
        .btn-launch { 
            background: linear-gradient(45deg, #007bff, #6610f2); 
            font-size: 18px; 
            padding: 15px 30px;
            box-shadow: 0 4px 15px rgba(0,123,255,0.4);
            transition: transform 0.2s;
        }
        .btn-launch:hover { transform: scale(1.05); }
    </style>
</head>
<body data-php-gen-time="<?php echo $php_total_time_ms; ?>">
    
    <div class="container">
        <div class="block b1">Лабораторна робота №5 (High Load)</div>
        <div class="block x">Lab 5</div>

        <div class="block b2">
            <h4>Завдання (Варіант 2):</h4>
            <ul style="font-size: 0.9em; padding-left: 20px;">
                <li>Дві кульки рухаються по екрану.</li>
                <li>При зіткненні зі стінками - відбиваються.</li>
                <li>При зіткненні одна з одною - зупиняються.</li>
                <li>Усі події записуються в БД (Stream & Batch).</li>
            </ul>
        </div>
        
        <div class="block b3 menu">
             <h3>Меню</h3>
             <ul>
                <?php foreach ($menu as $link => $name) {
                    $activeClass = (basename($_SERVER['PHP_SELF']) == $link) ? 'active' : '';
                    echo "<li><a href='$link' class='$activeClass'>$name</a></li>";
                } ?>
             </ul>
        </div>
        
        <div class="block b4" style="text-align: center; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3>Інтерактивна анімація</h3>
            <p>Натисніть кнопку нижче, щоб відкрити вікно симуляції:</p>
            
            <button id="btn-open-work" class="btn btn-lab btn-launch">
                ЗАПУСТИТИ АНІМАЦІЮ
            </button>

            <div id="results-info" style="margin-top: 20px; font-style: italic; color: gray;">
                Результати запису в БД з'являться знизу після закриття вікна.
            </div>
        </div>

        <div class="block b5">
            <h4>Останні події з БД:</h4>
            <div id="results-table" style="overflow-x: auto;">
                <em>Даних поки немає...</em>
            </div>
        </div>
        
        <div class="block b6">
            <p>Футер сайту</p>
            <div class="block y">2025</div>
        </div>
    </div>

    <div id="work-overlay">
        <div id="controls-area">
            <div>
                <button id="btn-start" class="btn-lab btn-start">Start</button>
                <button id="btn-stop" class="btn-lab btn-stop" style="display:none;">Stop</button>
                <button id="btn-reload" class="btn-lab" style="background:#17a2b8; display:none;">Reload</button>
            </div>
            <div style="font-weight: bold; color: #333;">Симуляція навантаження</div>
            <button id="btn-close" class="btn-lab btn-close">Close & Save</button>
        </div>

        <div id="anim-area">
            <div id="ball1" class="ball"></div>
            <div id="ball2" class="ball"></div>
            <div id="log-console">Console ready...</div>
        </div>
    </div>

    <script src="lab5.js?v=<?php echo time(); ?>"></script>
</body>
</html>