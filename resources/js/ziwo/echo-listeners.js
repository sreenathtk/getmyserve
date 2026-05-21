import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster:  'pusher',
    key:          import.meta.env.VITE_PUSHER_APP_KEY,
    cluster:      import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS:     true,
    authEndpoint: '/broadcasting/auth',
});

window.Echo.private('admin-calls')
    .listen('.call.started',        (data) => window.dispatchEvent(new CustomEvent('ziwo:call-started',   { detail: data })))
    .listen('.call.answered',       (data) => window.dispatchEvent(new CustomEvent('ziwo:call-answered',  { detail: data })))
    .listen('.call.ended',          (data) => window.dispatchEvent(new CustomEvent('ziwo:call-ended',     { detail: data })))
    .listen('.call.missed',         (data) => {
        window.dispatchEvent(new CustomEvent('ziwo:call-missed', { detail: data }));
        showBrowserNotification('Missed Call', 'From ' + (data.from || 'unknown number'));
    })
    .listen('.call.transferred',    (data) => window.dispatchEvent(new CustomEvent('ziwo:call-transferred', { detail: data })))
    .listen('.recording.ready',     (data) => window.dispatchEvent(new CustomEvent('ziwo:recording-ready', { detail: data })))
    .listen('.agent.status_changed',(data) => window.dispatchEvent(new CustomEvent('ziwo:agent-status',   { detail: data })));

function showBrowserNotification(title, body) {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(title, { body, icon: '/images/logo.png' });
    } else if ('Notification' in window && Notification.permission !== 'denied') {
        Notification.requestPermission().then((perm) => {
            if (perm === 'granted') new Notification(title, { body });
        });
    }
}
