self.addEventListener('push', function (event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    const sendNotification = message => {
        const { title, body, data } = message;

        return self.registration.showNotification(title, { body, data });
    };

    if (event.data) {
        const message = event.data.json();
        event.waitUntil(sendNotification(message));
    }
});

self.addEventListener('notificationclick', function(event) {
    const clickedNotification = event.notification;
    clickedNotification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data.url)
    );
});