// public/firebase-messaging-sw.js

// Import Firebase scripts (Make sure to use the compat versions for the service worker)
importScripts('[https://www.gstatic.com/firebasejs/10.8.0/firebase-app-compat.js](https://www.gstatic.com/firebasejs/10.8.0/firebase-app-compat.js)');
importScripts('[https://www.gstatic.com/firebasejs/10.8.0/firebase-messaging-compat.js](https://www.gstatic.com/firebasejs/10.8.0/firebase-messaging-compat.js)');

// Initialize Firebase (Replace with your actual config from Step 1)
firebase.initializeApp({
    apiKey: "AIzaSyB1EIwyQAuVb2D8m2zzQ6hTDZyp9_sJ5OI",
    authDomain: "talmaza-dc8e8.firebaseapp.com",
    projectId: "talmaza-dc8e8",
    storageBucket: "talmaza-dc8e8.firebasestorage.app",
    messagingSenderId: "1015353162177",
    appId: "1:1015353162177:web:91b8158f1311cd3e50d740",
    measurementId: "G-SK6VVWVPRR"
});

const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage(function(payload) {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: '/icons/icon.png' // Add your app icon path here
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});
