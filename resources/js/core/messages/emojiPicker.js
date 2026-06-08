import { autosizeInput } from './messageUtils.js';

const EMOJIS = [
    '😀', '😃', '😄', '😁', '😆', '😅', '🤣', '😂',
    '🙂', '😉', '😊', '😇', '🥰', '😍', '🤩', '😘',
    '😗', '😋', '😛', '😜', '🤪', '😝', '🤑', '🤗',
    '🤭', '🤫', '🤔', '🤐', '😏', '😒', '🙄', '😬',
    '😌', '😔', '😪', '😴', '😷', '🥳', '😎', '🤓',
    '🥺', '😢', '😭', '😤', '😡', '🤬', '💀', '☠️',
    '👍', '👎', '👏', '🙌', '🤝', '✌️', '🤞', '💪',
    '❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '💕',
    '💯', '🔥', '⭐', '✨', '🎉', '🎊', '🎁', '🏆',
    '✅', '❌', '👋', '🙏', '🚀', '💀', '👀', '🫶',
];

export function initEmojiPicker() {
    const btn = document.getElementById('msgsEmojiBtn');
    if (!btn) return;

    const picker = buildPicker();
    if (!picker) return;

    let isOpen = false;

    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        isOpen ? close() : open();
    });

    function open() {
        isOpen = true;
        picker.classList.add('active');
        picker.setAttribute('aria-hidden', 'false');
    }

    function close() {
        isOpen = false;
        picker.classList.remove('active');
        picker.setAttribute('aria-hidden', 'true');
    }

    picker.addEventListener('click', (e) => {
        const item = e.target.closest('.emoji-item');
        if (item) insertEmoji(item.dataset.emoji);
    });

    document.addEventListener('click', (e) => {
        if (isOpen && !picker.contains(e.target) && e.target !== btn && !btn.contains(e.target)) {
            close();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isOpen) close();
    });

    function insertEmoji(emoji) {
        const input = document.getElementById('msgsInput');
        if (!input) return;

        const start = input.selectionStart;
        const end = input.selectionEnd;
        input.value = input.value.slice(0, start) + emoji + input.value.slice(end);
        input.selectionStart = input.selectionEnd = start + emoji.length;
        input.focus();
        input.dispatchEvent(new Event('input'));
        autosizeInput(input);
    }
}

function buildPicker() {
    const inputBar = document.getElementById('msgsInputBar');
    if (!inputBar) return null;

    const picker = document.createElement('div');
    picker.id = 'emojiPicker';
    picker.className = 'emoji-picker';
    picker.setAttribute('aria-hidden', 'true');
    picker.setAttribute('role', 'dialog');
    picker.setAttribute('aria-label', 'Selector de emojis');

    const grid = document.createElement('div');
    grid.className = 'emoji-grid';

    EMOJIS.forEach(emoji => {
        const item = document.createElement('button');
        item.type = 'button';
        item.className = 'emoji-item';
        item.dataset.emoji = emoji;
        item.textContent = emoji;
        grid.appendChild(item);
    });

    picker.appendChild(grid);
    inputBar.style.position = 'relative';
    inputBar.appendChild(picker);

    return picker;
}
