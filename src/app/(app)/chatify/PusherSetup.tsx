import Pusher from 'pusher-js';
import Echo from 'laravel-echo';
import axios from 'axios';

window.Pusher = Pusher;
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const PusherSetup = (AuthToken) => {
    console.log("PusherSetup=",AuthToken);
    return new Echo({
        authEndpoint: process.env['NEXT_PUBLIC_BACKEND_URL'] + '/broadcasting/auth',
        // authEndpoint:  process.env['NEXT_PUBLIC_BACKEND_URL'] + '/api/chat/chat-auth',
        broadcaster: 'pusher',
        key: process.env.NEXT_PUBLIC_PUSHER_APP_KEY,
        cluster: process.env.NEXT_PUBLIC_PUSHER_APP_CLUSTER ?? 'mt1',
        wsHost: process.env.NEXT_PUBLIC_PUSHER_HOST
          ? process.env.NEXT_PUBLIC_PUSHER_HOST
          : `ws-${process.env.NEXT_PUBLIC_PUSHER_APP_CLUSTER}.pusher.com`,
        wsPort: process.env.NEXT_PUBLIC_PUSHER_PORT ?? 80,
        wssPort: process.env.NEXT_PUBLIC_PUSHER_PORT ?? 443,
        forceTLS: (process.env.NEXT_PUBLIC_PUSHER_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
        auth: {
            headers: {
                Authorization: 'Bearer ' + AuthToken,
            },
        },
    });
};
export default PusherSetup;
