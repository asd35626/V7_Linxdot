importScripts('https://www.gstatic.com/firebasejs/4.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/4.10.1/firebase-messaging.js');

var config = {
  apiKey: "AIzaSyDapoQkXi0LKJRfzHtLaqWqOg35QLRXZJU",
  authDomain: "starone-app.firebaseapp.com",
  databaseURL: "https://starone-app.firebaseio.com",
  projectId: "starone-app",
  storageBucket: "starone-app.appspot.com",
  messagingSenderId: "482115438497"
};
firebase.initializeApp(config);


const messaging = firebase.messaging();
