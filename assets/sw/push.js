"use strict";

importScripts("https://www.gstatic.com/firebasejs/4.8.1/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/4.8.1/firebase-messaging.js");

firebase.initializeApp({
  messagingSenderId: "[[SENDER_ID]]",
});

const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function (payload) {
  let obj = JSON.parse(payload.data.notification);

  self.registration.showNotification(obj.title, {
    body: obj.body || "",
    icon: obj.icon || void 0,
    image: obj.image || void 0,
    imageUrl: obj.image || void 0,
    vibrate: obj.vibrate || !0,
    badge: obj.badge || void 0,
    sound: obj.sound || void 0,
    sticky: obj.sticky || !0,
    requireInteraction: obj.requireInteraction || !0,
    actions: obj.actions || [],
    data: {
      url: obj.url,
      eventUrl: obj.eventUrl || "/",
    },
    renotify: obj.renotify || !1,
    tag: obj.notif_id || "id1",
  });
});

self.addEventListener("notificationclick", function (event) {
  var chain = [],
    noticeData = event.notification.data;
  chain.push(clients.openWindow(noticeData.url)),
    event.notification.close(),
    event.waitUntil(Promise.all(chain));
});
