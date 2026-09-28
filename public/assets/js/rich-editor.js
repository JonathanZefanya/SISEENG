/**
 * Rich text editor (Quill) untuk textarea dengan atribut data-rich-editor.
 *
 * Textarea asli tetap menjadi field yang dikirim ke server; isinya
 * disinkronkan dari editor saat submit. Server tetap WAJIB menyanitasi
 * HTML (lihat Core\HtmlSanitizer) karena request bisa dibuat tanpa editor.
 */
(function () {
    if (typeof Quill === 'undefined') return;

    const toolbar = [
        [{ header: [2, 3, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        ['blockquote', 'link'],
        [{ align: [] }],
        ['clean']
    ];

    document.querySelectorAll('textarea[data-rich-editor]').forEach(function (textarea) {
        // Toolbar Quill disisipkan sebelum container, jadi keduanya dibungkus wrapper
        const wrapper = document.createElement('div');
        wrapper.className = 'rich-editor';
        wrapper.style.setProperty('--editor-min-height', (textarea.dataset.minHeight || 300) + 'px');
        const container = document.createElement('div');
        wrapper.appendChild(container);
        textarea.after(wrapper);
        textarea.classList.add('d-none');

        const isRequired = textarea.hasAttribute('required');
        textarea.removeAttribute('required'); // field tersembunyi tidak bisa difokuskan browser

        const quill = new Quill(container, {
            theme: 'snow',
            placeholder: textarea.getAttribute('placeholder') || '',
            modules: {
                toolbar: toolbar,
                clipboard: { matchVisual: false }
            },
            formats: ['header', 'bold', 'italic', 'underline', 'strike', 'list', 'blockquote', 'link', 'align']
        });

        if (textarea.value.trim() !== '') {
            quill.setContents(quill.clipboard.convert({ html: textarea.value }), 'silent');
        }

        const label = document.querySelector('label[for="' + textarea.id + '"]');
        if (label) {
            label.addEventListener('click', function () { quill.focus(); });
        }

        const sync = function () {
            const isEmpty = quill.getText().trim() === '';
            // getSemanticHTML menghasilkan <ul>/<ol> standar; Quill 2.0.3 mengubah spasi jadi &nbsp;
            textarea.value = isEmpty ? '' : quill.getSemanticHTML().replace(/&nbsp;/g, ' ');
            return isEmpty;
        };

        quill.on('text-change', function () {
            container.classList.remove('is-invalid');
        });

        textarea.form.addEventListener('submit', function (e) {
            if (sync() && isRequired) {
                e.preventDefault();
                e.stopImmediatePropagation();
                container.classList.add('is-invalid');
                container.scrollIntoView({ behavior: 'smooth', block: 'center' });
                quill.focus();
            }
        });
    });
})();
