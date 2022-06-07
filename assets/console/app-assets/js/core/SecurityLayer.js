const LogOut = () => {
  $("#ContentDiv").attr("style", "pointer-events:none;opacity:0.5");
  $.post(
    InternalAjaxHost + "web-service/security/layer/logout",
    { logout: 1 },
    (d) => {
      window.location = InternalAjaxHost + "go?href=/login";
    }
  );
};

const Timer = (duration, random) => {
  $("#timerInput").val(random);
  let timer = duration,
    minutes,
    seconds;
  const CountInterval = setInterval(function () {
    if ($("#timerInput").val() != random) {
      clearInterval(CountInterval);
    } else {
      minutes = parseInt(timer / 60, 10);
      seconds = parseInt(timer % 60, 10);

      minutes = minutes < 10 ? "0" + minutes : minutes;
      seconds = seconds < 10 ? "0" + seconds : seconds;

      $("#Min").text(minutes);
      $("#Sec").text(seconds);
    }
    if (--timer < 0) {
      if ($("#selectedMethod").val() != "Authenticator") {
        $("#TimeLeftArea").hide();
        $("#SendAgain").fadeIn();
      }
      clearInterval(CountInterval);
    }
  }, 1000);
};

const SendAgain = () => {
  $("#SendAgain").hide();
  const withWhat = $("#selectedMethod").val();
  VerifyWith(withWhat);
};

const ChangeMethod = () => {
  const withWhat = $("#selectedMethod").val();
  $("button").attr("style", "pointer-events:none;opacity:0.5");
  $('[data-verify-src="' + withWhat + '"]').addClass("m-progress");
  $("#SecurityArea2").hide();
  $("#SecurityArea1").fadeIn();
  $("#Min").text("{min}");
  $("#Sec").text("{sec}");
  setTimeout(() => {
    $("button").removeAttr("style");
    $('[data-verify-src="' + withWhat + '"]').removeClass("m-progress");
  }, 500);
};

const VerifyWith = (withWhat) => {
  $("#selectedMethod").val(withWhat);
  $("button").attr("style", "pointer-events:none;opacity:0.5");
  $('[data-verify-src="' + withWhat + '"]').addClass("m-progress");
  setTimeout(() => {
    $.post(
      InternalAjaxHost + "web-service/security/layer/select",
      { selected: withWhat },
      (j) => {
        try {
          const Jsn = JSON.parse(j);

          if (Jsn.status == "success") {
            $("#SecurityArea1").hide();
            $("#SecurityArea2").fadeIn();

            if (Jsn.pinSendedMethod == "Authenticator") {
              $("#SendAgain").hide();
              $("#TimeLeftArea").hide();
            } else {
              $("#SendAgain").hide();
              $("#TimeLeftArea").show();
            }
            Timer(Jsn.pinLeftSecond, Jsn.TimerRandom);
            $("#SendedInfo").text(Jsn.SendedInfo);
            $("#pincode-input1").data("plugin_pincodeInput").focus();
          }
        } catch (error) {
          toastr.error(
            "There was a problem processing the request. Please try again later.",
            "Something were wrong!",
            {
              closeButton: true,
              timeOut: 5000,
            }
          );
        }

        $("button").removeAttr("style");
        $('[data-verify-src="' + withWhat + '"]').removeClass("m-progress");
      }
    ).fail(() => {
      toastr.error(
        "There was a problem processing the request. Please try again later.",
        "Something were wrong!",
        {
          closeButton: true,
          timeOut: 5000,
        }
      );
      $("button").removeAttr("style");
      $('[data-verify-src="' + withWhat + '"]').removeClass("m-progress");
    });
  }, 500);
};

if ($("[data-verify-src]").length < 2) {
  $("[data-verify-src]").click();
}
/////////////////////////////////////////////
$("#pincode-input1")
  .pincodeInput({
    inputs: 6,
    complete: function (value, e, errorElement) {
      $(".pincode-input-text").blur();
      $("#PinCodeArea").attr("style", "pointer-events:none;opacity:0.5");
      $.post(
        InternalAjaxHost + "web-service/security/layer/verify",
        { pin: value, selected: $("#selectedMethod").val() },
        (jn) => {
          try {
            const JsR = JSON.parse(jn);
            if (JsR.process == "success") {
              setTimeout(() => {
                toastr.success(JsR.message, JsR.title, {
                  closeButton: true,
                  timeOut: 8000,
                });
                $("#SecurityArea1").hide();
                $("#SecurityArea2").hide();
                $("#LogOutFooter").hide();
                $("#SecurityArea3").show();
                $("#SccText").fadeIn();
                window.location =
                  InternalAjaxHost + "go?href=" + window.location.href;
                $(".pincode-input-text").val("");
              }, 1500);
            } else {
              setTimeout(() => {
                toastr.warning(JsR.message, JsR.title, {
                  closeButton: true,
                  timeOut: 8000,
                });
                $("#pincode-input1").data("plugin_pincodeInput").focus();
                $(".pincode-input-text").val("");
                $("#PinCodeArea").removeAttr("style");
              }, 1000);
            }
          } catch (error) {
            toastr.error(
              "Something were wrong. Please try again later.",
              "System Error!",
              {
                closeButton: true,
                timeOut: 8000,
              }
            );
          }
        }
      ).fail(() => {
        toastr.error(
          "Something were wrong. Please try again later.",
          "System Error!",
          {
            closeButton: true,
            timeOut: 8000,
          }
        );
      });
    },
  })
  .data("plugin_pincodeInput");
