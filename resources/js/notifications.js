document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('notifications-toggle');
    const panel = document.getElementById('notifications-panel');
    const list = document.getElementById('notifications-list');
    const badge = document.getElementById('notifications-badge');
    const countLabel = document.getElementById('notifications-count');

    if (!toggle) return;

    toggle.addEventListener('click', async function (e) {
        panel.classList.toggle('hidden');

        // mark opened - fetch notifications
        if (!panel.classList.contains('hidden')) {
            await loadNotifications();
        }
    });

    async function loadNotifications() {
        try {
            const res = await fetch('/settings/api/notifications');
            if (!res.ok) return;
            const notifications = await res.json();

            renderNotifications(notifications);
            await updateUnreadCount();
        } catch (err) {
            console.error('Failed to load notifications', err);
        }
    }

    function renderNotifications(items) {
        list.innerHTML = '';
        if (!items || items.length === 0) {
            list.innerHTML = '<div class="p-4 text-sm text-gray-500">No notifications</div>';
            return;
        }

        for (const n of items) {
            const div = document.createElement('div');
            div.className = 'p-4 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700';
            div.innerHTML = `
                <div class="flex justify-between items-start">
                    <div>
                        <div class="font-semibold text-sm">${escapeHtml(n.title)}</div>
                        <div class="text-xs text-gray-500 mt-1">${escapeHtml(n.message || '')}</div>
                        <div class="text-xs text-gray-400 mt-1">${timeAgo(n.created_at || '')}</div>
                    </div>
                    <div class="ms-2">
                        ${n.read_at ? '' : '<button data-id="'+n.id+'" class="mark-read inline-flex items-center px-2 py-1 text-xs bg-indigo-600 text-white rounded">Mark</button>'}
                    </div>
                </div>
            `;
            list.appendChild(div);
        }

        // attach mark handlers
        document.querySelectorAll('.mark-read').forEach(btn => {
            btn.addEventListener('click', async function () {
                const id = this.getAttribute('data-id');
                await fetch(`/settings/api/notifications/${id}/read`, { method: 'PATCH', headers: {'X-CSRF-TOKEN': getCsrfToken()} });
                await loadNotifications();
            });
        });
    }

    async function updateUnreadCount() {
        try {
            const res = await fetch('/settings/api/notifications/unread-count');
            if (!res.ok) return;
            const json = await res.json();
            const count = json.unread || 0;
            if (count > 0) {
                badge.classList.remove('hidden');
                badge.textContent = count;
                countLabel.textContent = count + ' new';
            } else {
                badge.classList.add('hidden');
                badge.textContent = '';
                countLabel.textContent = '0 new';
            }
        } catch (err) {
            console.error('Failed to load unread count', err);
        }
    }

    // Poll unread count every 30s
    setInterval(updateUnreadCount, 30000);
    updateUnreadCount();

    function escapeHtml(str) {
        return (str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function timeAgo(iso) {
        if (!iso) return '';
        const d = new Date(iso);
        const diff = Math.floor((Date.now() - d.getTime()) / 1000);
        if (diff < 60) return diff + ' seconds ago';
        if (diff < 3600) return Math.floor(diff / 60) + ' minutes ago';
        if (diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
        return Math.floor(diff / 86400) + ' days ago';
    }

    function getCsrfToken() {
        const el = document.querySelector('meta[name="csrf-token"]');
        return el ? el.getAttribute('content') : '';
    }

});
