import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

// Initialize Pusher with your Pusher app key
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'your_app_key',
    cluster: 'your_app_cluster',
    encrypted: true,
});

// Listen for the NewImageEvent on the new-image-channel
window.Echo.channel('new-image-channel')
    .listen('.NewImageEvent', (event) => {
        // Handle the event and update the user interface with the new image
        console.log(event.imageData);
        // Update the user interface with the new image data without reloading the page
    });
