const jsStart = performance.now();

document.addEventListener('DOMContentLoaded', () => {
    console.log("FINAL SCRIPT LOADED");

    // Отримуємо ім'я поточної сторінки
    const pageName = document.body.getAttribute('data-page-name');
    
    // Список сторінок, де ДОЗВОЛЕНО редагування (Лаб 3)
    const editablePages = ['page3.php', 'page4.php', 'page5.php'];

    // Перевіряємо: чи ми на сторінці з редагуванням?
    const isLab3Active = editablePages.includes(pageName);

    // ===============================================
    // ЛОГІКА ЛАБОРАТОРНОЇ №3 (Редагування блоків)
    // ===============================================
    if (isLab3Active) {
        const blocks = document.querySelectorAll('.block');

        blocks.forEach(block => {
            // Фільтр: не чіпаємо меню та системні блоки
            if (block.classList.contains('menu') || 
                block.classList.contains('x') || 
                block.classList.contains('y')) {
                return;
            }

            // Тільки на сторінках 3, 4, 5 додаємо візуальні ефекти
            block.style.cursor = "pointer";
            block.title = "Натисніть для редагування";
            
            block.onmouseover = () => block.style.outline = "2px dashed red";
            block.onmouseout = () => block.style.outline = "none";

            // Додаємо клік для редагування
            block.addEventListener('click', function(e) {
                // Якщо форма вже відкрита - виходимо
                if (this.querySelector('.edit-form')) return;
                e.stopPropagation();

                const currentHTML = this.innerHTML;
                
                // Парсинг контенту (текст, список, фото)
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = currentHTML;
                
                // Очищаємо від зайвих тегів, щоб отримати чистий текст
                const cleanText = tempDiv.cloneNode(true);
                cleanText.querySelectorAll('ul, ol, img, form').forEach(el => el.remove());
                const textVal = cleanText.innerText.trim();
                
                let listVal = '';
                const ulOriginal = this.querySelector('ul');
                if (ulOriginal) listVal = Array.from(ulOriginal.querySelectorAll('li')).map(li => li.innerText).join('\n');
                
                let photoVal = '';
                const imgOriginal = this.querySelector('img');
                if (imgOriginal) photoVal = imgOriginal.src;

                // Створюємо форму
                const form = document.createElement('form');
                form.className = 'edit-form';
                form.style.background = "#fff";
                form.style.padding = "10px";
                form.style.border = "2px solid blue";
                form.style.cursor = "default";
                form.onclick = (ev) => ev.stopPropagation(); 
                
                form.innerHTML = `
                    <div style="margin-bottom:5px;"><strong>Редагування</strong></div>
                    <label>Текст:</label><br>
                    <textarea name="text" rows="3" style="width:100%;">${textVal}</textarea><br>
                    <label>Список (новий рядок = пункт):</label><br>
                    <textarea name="list" rows="3" style="width:100%;">${listVal}</textarea><br>
                    <label>Фото (URL):</label><br>
                    <input name="photo" type="text" style="width:100%;" value="${photoVal}"><br><br>
                    <button type="submit" style="background:green; color:white; border:none; padding:5px 10px; cursor:pointer;">Зберегти</button>
                    <button type="button" class="cancel-btn" style="background:gray; color:white; border:none; padding:5px 10px; cursor:pointer;">Скасувати</button>
                `;

                this.innerHTML = '';
                this.appendChild(form);
                this.style.outline = "none"; // Прибираємо червону рамку поки редагуємо

                // Кнопка Скасувати
                form.querySelector('.cancel-btn').onclick = (ev) => {
                    ev.stopPropagation();
                    this.innerHTML = currentHTML;
                };

                // Кнопка Зберегти
                form.onsubmit = (ev) => {
                    ev.preventDefault();
                    const btn = form.querySelector('button[type="submit"]');
                    btn.innerText = "Збереження...";

                    const obj = {
                        text: form.text.value.trim(),
                        list: form.list.value.split('\n').map(x => x.trim()).filter(x => x),
                        photo: form.photo.value.trim()
                    };

                    // Визначаємо ID блоку для бази
                    let blockId = 'unknown';
                    block.classList.forEach(cls => {
                        if (['b1', 'b2', 'b4', 'b5', 'b6'].includes(cls)) blockId = cls;
                    });

                    fetch('save_content.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            page_name: pageName,
                            block_id: blockId,
                            content: JSON.stringify(obj)
                        })
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            let newHtml = '';
                            if (obj.text) newHtml += `<div>${obj.text.replace(/\n/g, '<br>')}</div>`;
                            if (obj.list.length) newHtml += `<ul>${obj.list.map(i => `<li>${i}</li>`).join('')}</ul>`;
                            if (obj.photo) newHtml += `<img src="${obj.photo}" style="max-width:100%; max-height:200px;">`;
                            block.innerHTML = newHtml || '&nbsp;';
                        } else {
                            alert("Помилка: " + res.message);
                            block.innerHTML = currentHTML;
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert("Помилка мережі");
                        block.innerHTML = currentHTML;
                    });
                };
            });
        });
    }

    // ===============================================
    // ЛОГІКА ЛАБОРАТОРНОЇ №4 (Builder & Viewer)
    // Працює скрізь, де є відповідні контейнери (Index та Page 2)
    // ===============================================
    
    const builderContainer = document.getElementById('builder-container');
    const collapseRoot = document.getElementById('collapse-root');
    
    if (builderContainer) initBuilder(builderContainer);
    if (collapseRoot) initViewer(collapseRoot);

    showTimeStats();
});

// --- Допоміжні функції ---

function showTimeStats() {
    const jsEnd = performance.now();
    const php = document.body.getAttribute('data-php-gen-time') || 0;
    const db = document.body.getAttribute('data-db-time') || 0;
    
    const div = document.createElement('div');
    div.style.cssText = "position:fixed; bottom:0; left:0; background:rgba(0,0,0,0.8); color:white; padding:5px 10px; font-size:12px; z-index:9999;";
    div.innerHTML = `PHP: ${php}ms | DB: ${db}ms | JS: ${(jsEnd - jsStart).toFixed(2)}ms`;
    document.body.appendChild(div);
}

function initBuilder(container) {
    const btnAdd = document.getElementById('btn-add-row');
    const btnSave = document.getElementById('btn-save-server');
    
    fetch('get_content.php?page_name=shared_storage&block_id=collapse_data')
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success' && res.data) {
                try {
                    const items = JSON.parse(res.data);
                    if(Array.isArray(items)) items.forEach(i => addRow(container, i.title, i.content));
                } catch(e){}
            }
        });

    btnAdd.onclick = () => addRow(container);
    btnSave.onclick = () => {
        const data = [];
        container.querySelectorAll('.builder-row').forEach(row => {
            data.push({
                title: row.querySelector('.inp-title').value,
                content: row.querySelector('.inp-text').value
            });
        });
        const status = document.getElementById('status-msg');
        status.innerText = "Збереження...";
        fetch('save_content.php', {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body: JSON.stringify({page_name:'shared_storage', block_id:'collapse_data', content:JSON.stringify(data)})
        }).then(r=>r.json()).then(d=>{
            status.innerText = d.status==='success'?'ОК!':'Помилка';
        });
    };
}

function addRow(container, t='', c='') {
    const div = document.createElement('div');
    div.className = 'builder-row';
    div.innerHTML = `
        <input type="text" class="inp-title" value="${t.replace(/"/g,'&quot;')}" placeholder="Заголовок" style="width:100%; margin-bottom:5px;">
        <textarea class="inp-text" placeholder="Вміст" style="width:100%;">${c}</textarea>
        <button onclick="this.parentElement.remove()" style="background:red; color:white; border:none; margin-top:5px;">Видалити</button>
    `;
    container.appendChild(div);
}

function initViewer(root) {
    const update = async () => {
        try {
            const r = await fetch('get_content.php?page_name=shared_storage&block_id=collapse_data');
            const d = await r.json();
            if(d.status === 'success') {
                const currentHash = JSON.stringify(d.data);
                if(root.dataset.hash === currentHash) return;
                root.dataset.hash = currentHash;
                
                root.innerHTML = '';
                const items = JSON.parse(d.data);
                items.forEach(item => {
                    const el = document.createElement('div');
                    el.style.borderBottom = "1px solid #ccc";
                    el.innerHTML = `<div style="padding:10px; font-weight:bold; cursor:pointer; background:#f0f0f0;">${item.title}</div>
                                    <div class="content" style="display:none; padding:10px;">${item.content}</div>`;
                    el.firstElementChild.onclick = () => {
                        const c = el.querySelector('.content');
                        c.style.display = c.style.display === 'none' ? 'block' : 'none';
                    };
                    root.appendChild(el);
                });
                document.getElementById('last-update').innerText = "Оновлено: " + new Date().toLocaleTimeString();
            }
        } catch(e){}
    };
    update();
    setInterval(update, 3000);
}