/*
Give the service worker access to Firebase Messaging.
Note that you can only use Firebase Messaging here, other Firebase libraries are not available in the service worker.
*/
importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-messaging.js');
   
/*
Initialize the Firebase app in the service worker by passing in the messagingSenderId.
* New configuration for app@pulseservice.com
*/
firebase.initializeApp({
      apiKey: "AIzaSyAUxMuVwHVoE_fBblD9ENER3ScZDrwGNpo",
      //databaseURL: "https://XXXX.firebaseio.com",
      authDomain: "trade-internal.firebaseapp.com",
      projectId: "trade-internal",
      storageBucket: "trade-internal.appspot.com",
      messagingSenderId: "466790444286",
      appId: "1:466790444286:web:aa177c15e00fc241be09af",
      measurementId: "G-VCECZ93QSQ"
    });
  
/*
Retrieve an instance of Firebase Messaging so that it can handle background messages.
*/
var myUrl = "";
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
     const notification = payload.data;
    console.log(
        "[firebase-messaging-sw.js] Received background message ",
        payload,
    );
    /* Customize notification here */
    const notificationTitle = "Background Message Title";
    const notificationOptions = {
        body: "Background Message body.",
        icon: "/itwonders-web-logo.png",
        click_action : payload.notification.click_action,
        actions: [{action: "open_url", title: "Read Now"}]
    };
    myUrl = notification.click_action;
    console.log(myUrl);
    self.addEventListener('notificationclick', function(event) {
        console.log('fdsfdsa');
        event.notification.close();
    });
    event.waitUntill(
        clients.openWindow(click_action)
        );
    return self.registration.showNotification(
        notificationTitle,
        notificationOptions,
    );
});