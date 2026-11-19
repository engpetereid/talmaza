import './bootstrap';

// If the CSRF token/session expired (HTTP 419), silently reload the page to get a
// fresh token instead of showing Livewire's "This page has expired" dialog.
document.addEventListener('livewire:init', () => {
    Livewire.hook('request', ({ fail }) => {
        fail(({ status, preventDefault }) => {
            if (status === 419) {
                preventDefault();
                window.location.reload();
            }
        });
    });
});

// Page restored from the back/forward cache (e.g. after logout or reopening the app)
// carries an old CSRF token -> reload it.
window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        window.location.reload();
    }
});
