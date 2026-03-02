// public/firebase-messaging-sw.js

// 1. Corrected Import Scripts (No Markdown, No ES Imports)
importScripts('https://www.gstatic.com/firebasejs/10.8.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.8.0/firebase-messaging-compat.js');

// 2. Initialize Firebase
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

// 3. Handle background messages
messaging.onBackgroundMessage(function(payload) {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    // const notificationTitle = payload.notification?.title || 'إشعار جديد';
    // const notificationOptions = {
    //     body: payload.notification?.body || '',
    //     icon: '/icons/icon.png'
    // };

    // self.registration.showNotification(notificationTitle, notificationOptions);
});
