/*

Give the service worker access to Firebase Messaging.

Note that you can only use Firebase Messaging here, other Firebase libraries are not available in the service worker.

*/

importScripts("https://www.gstatic.com/firebasejs/7.23.0/firebase-app.js");
importScripts(
  "https://www.gstatic.com/firebasejs/7.23.0/firebase-messaging.js"
);

/*

Initialize the Firebase app in the service worker by passing in the messagingSenderId.

* New configuration for app@pulseservice.com

*/

firebase.initializeApp({
  apiKey: "AIzaSyBNOw5AnVwyC_1sE_QbEANCBqlV5N9hdJI",
  authDomain: "laravel-social-media-api.firebaseapp.com",
  databaseURL: "https://laravel-social-media-api.firebaseio.com",
  projectId: "laravel-social-media-api",
  storageBucket: "laravel-social-media-api.appspot.com",
  messagingSenderId: "183068862244",
  appId: "1:183068862244:web:1f2f46a04d27d540faf5b1",
  measurementId: "G-Z54Q14V251",
});

/*

Retrieve an instance of Firebase Messaging so that it can handle background messages.

*/

const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function (payload) {
  console.log(
    "[firebase-messaging-sw.js] Received background message ",

    payload
  );

  /* Customize notification here */
  const notificationTitle = "Background Message Title";
  const notificationOptions = {
    body: "Background Message body.",
    icon: "/itwonders-web-logo.png",
  };

  return self.registration.showNotification(
    notificationTitle,
    notificationOptions
  );
});
