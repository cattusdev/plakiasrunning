const LOAD_NOTIFICATIONS_URL = 'includes/admin/ajax.php'; 
const MARK_AS_READ_URL = 'includes/admin/ajax.php';   
const DELETE_NOTIFICATION_URL = 'includes/admin/ajax.php'; 

const RELOAD_INTERVAL_MINUTES = 2;

const IDLE_TIMEOUT_MINUTES = 5;



let idleTime = 0;
let isUserIdle = false;

setInterval(() => {
    idleTime++;
    if (idleTime >= IDLE_TIMEOUT_MINUTES) {
        isUserIdle = true;
    }
}, 60_000); // 1 min

function resetIdleTimer() {
    idleTime = 0;
    isUserIdle = false;
}

window.addEventListener('mousemove', resetIdleTimer, false);
window.addEventListener('keypress', resetIdleTimer, false);
// Προαιρετικά και άλλα events (touchstart κλπ) αν χρειάζεται για mobile.


function loadNotifications() {
    if (isUserIdle) {
        console.log("User is idle; skipping notifications load.");
        return;
    }
    let csrf_token = document.getElementsByName('csrf_token')[0].getAttribute('content');


    $.ajax({
        url: LOAD_NOTIFICATIONS_URL,
        method: 'POST',
        dataType: 'json',
        data: {
            action: "fetchNotifications",
            csrf_token: csrf_token,
        },
        success: function (response) {
            if (!response.success) {
                console.warn("Could not load notifications:", response.errors);
                const container = $("#notifcontainer");
                container.empty();
                container.append(`
                <div class="text-center p-2 py-4 text-muted">
                    <i class="bi bi-info"></i> Δεν υπάρχουν ειδοποιήσεις
                </div>
            `);
                updateBadge(0);
                return;
            }
            // Διαμόρφωση και απόδοση των ειδοποιήσεων
            renderNotifications(response.notifications);
        },
        error: function (err) {
            console.error("Error fetching notifications:", err);
        }
    });
}

function renderNotifications(notifications) {
    const container = $("#notifcontainer");
    container.empty();

    let unreadCount = 0;
    if (!notifications || notifications.length === 0) {
        
        container.append(`
            <div class="text-center p-2 py-4 text-muted">
                <i class="bi bi-inbox"></i> Δεν υπάρχουν ειδοποιήσεις
            </div>
        `);
        updateBadge(0);
        return;
    }

    notifications.forEach(notif => {
        const isRead = notif.is_read == 1;
        if (!isRead) unreadCount++;

        const readClass = isRead ? 'notif-read' : 'notif-unread';
        const timeAgo = moment(notif.created_at).fromNow();

        let messageHtml = `<span>${notif.message}</span>`;
        if (notif.url) {
            messageHtml = `<a href="${notif.url}" style="font-size: 13px;" class="text-decoration-none text-dark">${notif.message}</a>`;
        }

        const notificationHtml = `
            <div class="notification-item d-flex flex-column align-items-start justify-content-center shadow-sm py-3 px-2 bg-white rounded border-top ${readClass}" data-id="${notif.id}">
                
                <!-- Title and Icon -->
                <h6 class="mb-3 fw-semibold text-dark d-flex justify-content-between font-sm w-100">
                    ${notif.title}
                    <i class="bi bi-bell-fill ${isRead ? 'text-secondary' : 'text-primary'} font-sm"></i>
                </h6>

                <!-- Message Content -->
                <div class="flex-grow-1 w-100 py-2 px-1 border rounded bg-light">
                    <p class="mb-0 text-muted">${messageHtml}</p>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2 align-items-end justify-content-end w-100 mt-2">
                    <button class="btn btn-sm btn-outline p-1 font-xs" title="Mark as Read" onclick="markNotificationAsRead(${notif.id})">
                        <i class="bi bi-eye${isRead ? '-slash' : ''}"></i>
                    </button>
                    <button class="btn btn-sm btn-outline p-1 font-xs" title="Delete" onclick="deleteNotification(${notif.id})">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- Timestamp -->
                <div class="d-flex justify-content-between align-items-center w-100 mt-1">
                    <small class="text-muted font-xs">${timeAgo}</small>
                </div>
            </div>
        `;

        container.append(notificationHtml);
    });

    updateBadge(unreadCount);
}


function updateBadge(unreadCount) {
    const badge = $("#notifbadge");
    if (unreadCount > 0) {
        badge.text(unreadCount);
        badge.show();
    } else {
        badge.hide();
    }
}

function formatDateTime(dateStr) {
    if (!dateStr) return "";
    const d = new Date(dateStr);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    const hours = String(d.getHours()).padStart(2, '0');
    const mins = String(d.getMinutes()).padStart(2, '0');
    return `${day}/${month}/${year} ${hours}:${mins}`;
}


function markNotificationAsRead(notificationId) {
    let csrf_token = document.getElementsByName('csrf_token')[0].getAttribute('content');

    $.ajax({
        url: MARK_AS_READ_URL,
        method: 'POST',
        data: {
            action: "markNotificationRead",
            csrf_token: csrf_token,
            notifID: notificationId 
        },
        dataType: 'json',
        success: function (response) {
            if (!response.success) {
                setNotification("Warning", errorMessage, "warning");
                console.warn("Could not mark notification as read:", response.errors);
                return;
            }
            setNotification("Action", response.message, "success");

            const item = $(`#notifcontainer .notification-item[data-id="${notificationId}"]`);
            item.removeClass('bg-white text-dark').addClass('bg-light text-muted');

            const eyeIcon = item.find('.bi-eye, .bi-eye-slash');
            eyeIcon.removeClass('bi-eye').addClass('bi-eye-slash');
            loadNotifications();
        },
        error: function (err) {
            setNotification("Error", "Failed to perform the action.", "error");
            console.error("Error marking notification as read:", err);
        }
    });
}

function deleteNotification(notificationId) {
    let csrf_token = document.getElementsByName('csrf_token')[0].getAttribute('content');

    $.ajax({
        url: DELETE_NOTIFICATION_URL,
        method: 'POST',
        data: {
            action: "delNotification",
            csrf_token: csrf_token,
            notifID: notificationId
        },
        dataType: 'json',
        success: function (response) {
            if (!response.success) {
                setNotification("Warning", errorMessage, "warning");
                console.warn("Could not delete notification:", response.errors);
                return;
            }

            setNotification("Action", response.message, "success");
            $(`#notifcontainer .notification-item[data-id="${notificationId}"]`).remove();
            loadNotifications();
        },
        error: function (err) {
            setNotification("Error", "Failed to perform the action.", "error");
            console.error("Error deleting notification:", err);
        }
    });
}


$(document).ready(function () {
    loadNotifications();

    setInterval(loadNotifications, RELOAD_INTERVAL_MINUTES * 60 * 1000);
});