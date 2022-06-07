$(document).ready(function () {
  $.post("/web-service/firebase/config", { load: "config" }, (configData) => {
    const firebaseConfig = JSON.parse(configData);

    firebase.initializeApp(firebaseConfig);

    const messaging = firebase.messaging();

    messaging.onMessage(function (payload) {
      let obj = JSON.parse(payload.data.notification);

      var notification = new Notification(obj.title, {
        body: obj.body || "",
        icon: obj.icon || void 0,
        image: obj.image || void 0,
        imageUrl: obj.image || void 0,
        vibrate: obj.vibrate || !0,
        badge: obj.badge || void 0,
        sound: obj.sound || void 0,
        sticky: obj.sticky || !0,
        requireInteraction: obj.requireInteraction || !0,
        data: {
          url: obj.url,
          eventUrl: obj.eventUrl || "/",
        },
        renotify: obj.renotify || !1,
        tag: obj.notif_id || "id1",
      });

      notification.onclick = function (notif) {
        console.log(notif);
      };
    });

    navigator.serviceWorker.register("/sw.js").then((registration) => {
      messaging.useServiceWorker(registration);
    });

    $("#SetID").on("click", () => {
      $("#SetID").attr("style", "pointer-events:none;opacity:0.5;");

      navigator.serviceWorker.register("/sw.js").then((registration) => {
        messaging
          .requestPermission()
          .then(function () {
            $("#T1").hide();
            $("#SetID").hide();
            $("#T2").fadeIn();
            messaging.getToken().then(function (currentToken) {
              const PageUrl = window.location.href;
              const SplitUrl = PageUrl.split("/");
              const PageToken = SplitUrl[4];
              const PageToken2 = SplitUrl[5];
              setTimeout(() => {
                $.post(
                  "/web-service/set/push/id",
                  {
                    Jwt: PageToken,
                    UrlToken: PageToken2,
                    FcmToken: currentToken,
                    Recaptcha: $("#ScKey").val(),
                  },
                  (j) => {
                    $(".f_static").hide();
                    $(".f_success").show();
                    setTimeout(() => {
                      window.close();
                    }, 2000);
                    RecaptchaG();
                  }
                ).fail(() => {
                  window.location = "";
                });
              }, 100);
            });
          })
          .catch(function (error) {
            $("#T2").hide();
            $("#T1").fadeIn();
            $("#SetID").removeAttr("style");
          });
      });
    });
  });
});
