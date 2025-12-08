document.addEventListener('DOMContentLoaded', () => {
    const workOverlay = document.getElementById('work-overlay');
    const animArea = document.getElementById('anim-area');
    const ball1 = document.getElementById('ball1'); // Red
    const ball2 = document.getElementById('ball2'); // Green
    const logConsole = document.getElementById('log-console');
    const resultsTable = document.getElementById('results-table');

    // Кнопки
    const btnOpen = document.getElementById('btn-open-work');
    const btnClose = document.getElementById('btn-close');
    const btnStart = document.getElementById('btn-start');
    const btnStop = document.getElementById('btn-stop');
    const btnReload = document.getElementById('btn-reload');

    let timerId = null;
    let localStorageData = []; // Для Методу 2 (накопичення)
    
    // Параметри кульок
    let b1 = { x: 0, y: 0, dx: 3, dy: 4, r: 10, color: 'red' };
    let b2 = { x: 0, y: 0, dx: -4, dy: 2, r: 10, color: 'green' };
    
    // Розміри поля
    let width = 0;
    let height = 0;

    // 1. Відкриття вікна
    btnOpen.onclick = () => {
        workOverlay.style.display = 'block';
        resetPositions();
    };

    // 2. Закриття вікна -> Відправка Batch даних -> Отримання звіту
    btnClose.onclick = () => {
        stopAnimation();
        workOverlay.style.display = 'none';
        
        // Метод 2: Відправляємо накопичені дані одним пакетом
        if (localStorageData.length > 0) {
            log("Відправка Batch даних (" + localStorageData.length + " записів)...");
            fetch('save_lab5_batch.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(localStorageData)
            })
            .then(r => r.json())
            .then(() => {
                // Після збереження завантажуємо таблицю
                fetch('get_lab5_stats.php')
                    .then(r => r.text())
                    .then(html => resultsTable.innerHTML = html);
                localStorageData = []; // Очистка
                localStorage.removeItem('lab5_events');
            });
        }
    };

    // 3. Старт
    btnStart.onclick = () => {
        btnStart.style.display = 'none';
        btnStop.style.display = 'inline-block';
        startAnimation();
        logEvent('button_click', 'Start pressed');
    };

    // 4. Стоп
    btnStop.onclick = () => {
        stopAnimation();
        btnStop.style.display = 'none';
        btnStart.style.display = 'inline-block';
        logEvent('button_click', 'Stop pressed');
    };

    // 5. Перезавантаження
    btnReload.onclick = () => {
        resetPositions();
        btnReload.style.display = 'none';
        btnStart.style.display = 'inline-block';
        logEvent('button_click', 'Reload pressed');
    };

    function resetPositions() {
        width = animArea.clientWidth;
        height = animArea.clientHeight;

        // Початкові позиції (згідно варіанту: біля стінок)
        b1.x = 0; // Ліва стінка
        b1.y = Math.random() * (height - 20);
        
        b2.x = Math.random() * (width - 20);
        b2.y = 0; // Верхня стінка

        updateDOM();
    }

    function startAnimation() {
        if (timerId) return;
        // Інтервал 40мс = 25 кадрів на секунду (досить часті запити на сервер)
        timerId = setInterval(updatePhysics, 40);
    }

    function stopAnimation() {
        clearInterval(timerId);
        timerId = null;
    }

    function updatePhysics() {
        width = animArea.clientWidth;
        height = animArea.clientHeight;

        // Рух
        b1.x += b1.dx;
        b1.y += b1.dy;
        b2.x += b2.dx;
        b2.y += b2.dy;

        let eventDetails = '';

        // Відбивання b1
        if (b1.x <= 0 || b1.x >= width - 20) { 
            b1.dx = -b1.dx; 
            eventDetails += 'Red hit Wall; '; 
        }
        if (b1.y <= 0 || b1.y >= height - 20) { 
            b1.dy = -b1.dy; 
            eventDetails += 'Red hit Floor/Ceiling; '; 
        }

        // Відбивання b2
        if (b2.x <= 0 || b2.x >= width - 20) { 
            b2.dx = -b2.dx; 
            eventDetails += 'Green hit Wall; '; 
        }
        if (b2.y <= 0 || b2.y >= height - 20) { 
            b2.dy = -b2.dy; 
            eventDetails += 'Green hit Floor/Ceiling; '; 
        }

        // Перевірка зіткнення (Collision)
        // Відстань між центрами
        let dx = (b1.x + 10) - (b2.x + 10);
        let dy = (b1.y + 10) - (b2.y + 10);
        let distance = Math.sqrt(dx*dx + dy*dy);

        if (distance < 20) { // 10+10 радіуси
            stopAnimation();
            btnStop.style.display = 'none';
            btnReload.style.display = 'inline-block';
            eventDetails += 'COLLISION!';
            alert('Зіткнення! Анімація зупинена.');
        }

        updateDOM();

        // Записуємо кожен крок (move) або подію удару
        logEvent(eventDetails ? 'event' : 'move', eventDetails || 'moving');
    }

    function updateDOM() {
        ball1.style.left = b1.x + 'px';
        ball1.style.top = b1.y + 'px';
        ball2.style.left = b2.x + 'px';
        ball2.style.top = b2.y + 'px';
    }

    function logEvent(type, text) {
        const now = new Date().toISOString(); // Клієнтський час
        
        // Візуальний лог
        const msg = `[${now.split('T')[1]}] ${type}: ${text}`;
        const p = document.createElement('div');
        p.innerText = msg;
        logConsole.prepend(p);

        // --- МЕТОД 1: Миттєва відправка (Stream) ---
        // Відправляємо AJAX запит на сервер ПРЯМО ЗАРАЗ
        const formData = new FormData();
        formData.append('method', 'stream');
        formData.append('event_type', type);
        formData.append('client_time', now);
        formData.append('details', text);
        
        // Використовуємо sendBeacon для надійності або fetch (без очікування відповіді для швидкості)
        navigator.sendBeacon('save_lab5_stream.php', formData);

        // --- МЕТОД 2: Накопичення (Batch) ---
        // Зберігаємо в масив, щоб відправити потім
        const record = {
            event_type: type,
            client_time: now,
            details: text
        };
        localStorageData.push(record);
        // Зберігаємо в localStorage про всяк випадок (якщо сторінка впаде)
        localStorage.setItem('lab5_events', JSON.stringify(localStorageData));
    }

    function log(txt) {
        const p = document.createElement('div');
        p.style.color = 'blue';
        p.innerText = txt;
        logConsole.prepend(p);
    }
});