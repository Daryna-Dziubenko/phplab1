const jsStart = performance.now();

const editableBlocks = Array.from(document.querySelectorAll('.block'))
    .filter(block => !block.classList.contains('menu'));

function showPhpTime() {
    const phpTime = document.body.getAttribute('data-php-time');
    if (phpTime) {
        const info = document.createElement('div');
        info.className = 'page-time';
        info.innerHTML = `Час генерації сторінки (PHP): <b>${phpTime} мс</b>`;
        document.body.prepend(info);
    }
}

function showJsTime(jsEnd) {
    const info = document.createElement('div');
    info.className = 'js-time';
    info.innerHTML = `Час підвантаження даних з localStorage (JS): <b>${(jsEnd - jsStart).toFixed(2)} мс</b>`;
    document.body.prepend(info);
}

editableBlocks.forEach((block, idx) => {
    const key = location.pathname + '_block_' + idx;
    const saved = localStorage.getItem(key);
    if (saved) {
        try {
            const obj = JSON.parse(saved);
            let html = '';
            if (obj.text) html += `<div>${obj.text}</div>`;
            if (obj.list && obj.list.length) html += `<ul>${obj.list.map(item => `<li>${item}</li>`).join('')}</ul>`;
            if (obj.photo) html += `<img src="${obj.photo}" style="max-width:100%;max-height:200px;">`;
            if (html) block.innerHTML = html;
        } catch {
            block.innerHTML = saved;
        }
    }
});

editableBlocks.forEach((block, idx) => {
    block.addEventListener('click', function (e) {
        if (block.querySelector('.edit-form')) return;
        e.stopPropagation();

        const currentContent = block.innerHTML;

        let textVal = '';
        let listVal = '';
        let photoVal = '';
        const key = location.pathname + '_block_' + idx;
        const saved = localStorage.getItem(key);
        if (saved) {
            try {
                const obj = JSON.parse(saved);
                if (obj.text) textVal = obj.text;
                if (obj.list) listVal = obj.list.join('\n');
                if (obj.photo) photoVal = obj.photo;
            } catch {}
        } else {
            const temp = document.createElement('div');
            temp.innerHTML = currentContent;
            const divs = temp.querySelectorAll('div');
            if (divs.length) {
                textVal = Array.from(divs).map(d => d.textContent).join('\n');
            } else {
                let clone = temp.cloneNode(true);
                clone.querySelectorAll('ul,ol').forEach(el => el.remove());
                clone.querySelectorAll('img').forEach(el => el.remove());
                textVal = clone.textContent.trim();
            }
            const ul = temp.querySelector('ul');
            if (ul) listVal = Array.from(ul.querySelectorAll('li')).map(li => li.textContent).join('\n');
            const img = temp.querySelector('img');
            if (img) photoVal = img.src;
        }

        const form = document.createElement('form');
        form.className = 'edit-form';
        form.innerHTML = `
            <label>Текст:</label><br>
            <textarea name="text" rows="3" style="width:90%;">${textVal}</textarea><br>
            <label>Список (кожен рядок — пункт):</label><br>
            <textarea name="list" rows="4" style="width:90%;">${listVal}</textarea><br>
            <label>Фото (URL):</label><br>
            <input name="photo" type="text" style="width:90%;" placeholder="URL зображення" value="${photoVal}"><br>
            <button type="submit">Зберегти</button>
            <button type="button" class="cancel-btn">Скасувати</button>
        `;
        block.innerHTML = '';
        block.appendChild(form);

        form.querySelector('.cancel-btn').onclick = function () {
            block.innerHTML = currentContent;
        };

        form.onsubmit = function (ev) {
            ev.preventDefault();
            const obj = {
                text: form.text.value.trim(),
                list: form.list.value.split('\n').map(x => x.trim()).filter(x => x),
                photo: form.photo.value.trim()
            };
            let html = '';
            if (obj.text) html += `<div>${obj.text}</div>`;
            if (obj.list.length) html += `<ul>${obj.list.map(item => `<li>${item}</li>`).join('')}</ul>`;
            if (obj.photo) html += `<img src="${obj.photo}" style="max-width:100%;max-height:200px;">`;
            block.innerHTML = html || '&nbsp;';
            localStorage.setItem(key, JSON.stringify(obj));
        };
    });
});

showPhpTime();
const jsEnd = performance.now();
showJsTime(jsEnd);