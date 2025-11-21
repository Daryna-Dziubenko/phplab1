const jsStart = performance.now();

document.addEventListener('DOMContentLoaded', () => {
    
    const builderContainer = document.getElementById('builder-container');
    const btnAdd = document.getElementById('btn-add-row');
    const btnSave = document.getElementById('btn-save-server');

    if (builderContainer && btnAdd && btnSave) {
        console.log('Init Editor Mode');

        function addBuilderRow(titleVal = '', textVal = '') {
            const rowDiv = document.createElement('div');
            rowDiv.className = 'builder-row';
            rowDiv.innerHTML = `
                <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                    <label><strong>Заголовок:</strong></label>
                    <button class="btn btn-remove" onclick="this.closest('.builder-row').remove()">Видалити</button>
                </div>
                <input type="text" class="inp-title" value="${titleVal}" placeholder="Назва секції">
                <label><strong>Вміст:</strong></label>
                <textarea class="inp-text" rows="3" placeholder="Текст, який розкривається...">${textVal}</textarea>
            `;
            builderContainer.appendChild(rowDiv);
        }

        btnAdd.addEventListener('click', () => {
            addBuilderRow();
        });

        btnSave.addEventListener('click', () => {
            const rows = document.querySelectorAll('.builder-row');
            const data = [];
            
            rows.forEach(row => {
                const title = row.querySelector('.inp-title').value.trim();
                const content = row.querySelector('.inp-text').value.trim();
                if(title) {
                    data.push({ title, content });
                }
            });

            const statusSpan = document.getElementById('status-msg');
            statusSpan.innerText = 'Відправка...';
            statusSpan.style.color = 'blue';

            fetch('save_c.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    page_name: 'shared_storage',
                    block_id: 'collapse_data',
                    content: JSON.stringify(data)
                })
            })
            .then(res => res.json())
            .then(res => {
                if(res.status === 'success') {
                    statusSpan.innerText = 'Успішно збережено!';
                    statusSpan.style.color = 'green';
                } else {
                    statusSpan.innerText = 'Помилка: ' + res.message;
                    statusSpan.style.color = 'red';
                }
            })
            .catch(err => {
                console.error(err);
                statusSpan.innerText = 'Помилка мережі';
                statusSpan.style.color = 'red';
            });
        });

        addBuilderRow('Приклад заголовка', 'Приклад тексту...');
    }


    const collapseRoot = document.getElementById('collapse-root');
    const updateLabel = document.getElementById('last-update');

    if (collapseRoot) {
        console.log('Init Viewer Mode');
        let currentDataHash = '';

        async function fetchUpdates() {
            try {
                const response = await fetch('get_content.php?page_name=shared_storage&block_id=collapse_data');
                const json = await response.json();

                if (json.status === 'success') {
                    if (json.data !== currentDataHash) {
                        currentDataHash = json.data;
                        renderAccordion(JSON.parse(json.data));
                        
                        const now = new Date();
                        if(updateLabel) updateLabel.innerText = 'Оновлено: ' + now.toLocaleTimeString();
                    }
                } else if (json.status === 'empty') {
                    collapseRoot.innerHTML = '<div style="padding:15px;">Даних поки немає. Додайте їх на Page 1.</div>';
                }
            } catch (error) {
                console.error('Polling error:', error);
            }
        }

        function renderAccordion(items) {
            collapseRoot.innerHTML = ''; 

            if (!Array.isArray(items) || items.length === 0) {
                collapseRoot.innerHTML = '<div style="padding:15px;">Список порожній</div>';
                return;
            }

            items.forEach(item => {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'collapse-item';

                const headerDiv = document.createElement('div');
                headerDiv.className = 'collapse-header';
                headerDiv.textContent = item.title;

                const contentDiv = document.createElement('div');
                contentDiv.className = 'collapse-content';
                contentDiv.innerHTML = `<div style="padding: 15px;">${item.content}</div>`;

                headerDiv.addEventListener('click', () => {
                    itemDiv.classList.toggle('active');
                });

                itemDiv.appendChild(headerDiv);
                itemDiv.appendChild(contentDiv);
                collapseRoot.appendChild(itemDiv);
            });
        }

        fetchUpdates();
        setInterval(fetchUpdates, 3000);
    }

    showPhpTime();
    const jsEnd = performance.now();
    showJsTime(jsEnd);
});

function showPhpTime() {
    const phpTime = document.body.getAttribute('data-php-gen-time'); 
    
    if (phpTime) {
        const info = document.createElement('div');
        info.className = 'page-time';
        // Стилі
        info.style.position = 'fixed';
        info.style.bottom = '10px';
        info.style.left = '10px';
        info.style.background = '#e9f5ff';
        info.style.padding = '5px 10px';
        info.style.border = '1px solid #b3d7f7';
        info.style.borderRadius = '5px';
        info.style.fontSize = '12px';
        
        info.innerHTML = `Час генерації (PHP): <b>${phpTime} мс</b>`;
        document.body.appendChild(info);
    }
}

function showJsTime(jsEnd) {
    const info = document.createElement('div');
    info.className = 'js-time';
    // Стилі
    info.style.position = 'fixed';
    info.style.bottom = '40px';
    info.style.left = '10px';
    info.style.background = '#e9f5ff';
    info.style.padding = '5px 10px';
    info.style.border = '1px solid #b3d7f7';
    info.style.borderRadius = '5px';
    info.style.fontSize = '12px';

    info.innerHTML = `Час підвантаження (JS): <b>${(jsEnd - jsStart).toFixed(2)} мс</b>`;
    document.body.appendChild(info);
}