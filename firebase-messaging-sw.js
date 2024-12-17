importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');
firebase.initializeApp({apiKey: "AIzaSyDFqJgQ_1metCxZ7cLljSOhhmb0A0uhOkw",authDomain: "brand-517d4.firebaseapp.com",projectId: "brand-517d4",storageBucket: "brand-517d4.appspot.com", messagingSenderId: "651078323597", appId: "1:651078323597:web:76418ec9adb125874a01c4"});
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function (payload) { return self.registration.showNotification(payload.data.title, { body: payload.data.body ? payload.data.body : '', icon: payload.data.icon ? payload.data.icon : '' }); });
