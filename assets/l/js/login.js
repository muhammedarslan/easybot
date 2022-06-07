$.urlParam = function (t) {
  var e = new RegExp("[?&]" + t + "=([^&#]*)").exec(window.location.search);
  return null !== e ? e[1] || 0 : InternalAjaxHost + "console";
};

$("#LoginForm").on("submit", () => {
  $("#AjaxContent").html("");
  const Email = $("#form_email").val();
  const Password = $("#form_password").val();
  $("#form_password").blur();

  if (Email != "" && Password != "") {
    $("#form_email").attr("style", "pointer-events:none;opacity:0.5;");
    $("#form_password").attr("style", "pointer-events:none;opacity:0.5;");
    $("#form_button").attr("style", "pointer-events:none;opacity:0.5;");
    setTimeout(() => {
      $.post(
        InternalAjaxHost + "web-service/login",
        $("#LoginForm").serialize(),
        (data) => {
          try {
            const JsonLogin = JSON.parse(data);

            if (JsonLogin.status == "success") {
              $("#AjaxContent").hide();
              $("#AjaxContent").html(
                '<div class="alert alert-' +
                  JsonLogin.label +
                  '" role="alert">' +
                  JsonLogin.message +
                  "</div>"
              );
              $("#AjaxContent").fadeIn();
              $("#form_email").removeAttr("style");
              $("#form_password").removeAttr("style");
              $("#form_button").removeAttr("style");
              $("#form_email").attr("disabled", "disabled");
              $("#form_button").attr("disabled", "disabled");
              window.location =
                InternalAjaxHost + "go?href=" + $.urlParam("next");
            } else {
              $("#AjaxContent").hide();
              $("#AjaxContent").html(
                '<div class="alert alert-' +
                  JsonLogin.label +
                  '" role="alert">' +
                  JsonLogin.message +
                  "</div>"
              );
              $("#AjaxContent").fadeIn();
              $("#form_password").val("");
              $("#form_email").removeAttr("style");
              $("#form_password").removeAttr("style");
              $("#form_button").removeAttr("style");
            }
          } catch (error) {
            //window.location = "";
          }
          RecaptchaG();
        }
      ).fail(() => {
        //window.location = "";
      });
    }, 1000);
  }
});

$("#LostPwForm").on("submit", () => {
  $("#AjaxContent").html("");
  $("#lost_form_email").blur();
  const LostEmail = $("#lost_form_email").val();

  if (LostEmail != "") {
    $("#lost_form_email").attr("style", "pointer-events:none;opacity:0.5;");
    $("#form_button_lost").attr("style", "pointer-events:none;opacity:0.5;");
    setTimeout(() => {
      $.post(
        InternalAjaxHost + "web-service/reset/password",
        $("#LostPwForm").serialize(),
        (data) => {
          try {
            const JsonLostPassword = JSON.parse(data);

            if (JsonLostPassword.status == "success") {
              $("#AjaxContent").hide();
              $("#AjaxContent").html(
                '<div class="alert alert-' +
                  JsonLostPassword.label +
                  '" role="alert">' +
                  JsonLostPassword.message +
                  "</div>"
              );
              $("#AjaxContent").fadeIn();
              $("#lost_form_email").removeAttr("style");
              $("#form_button_lost").removeAttr("style");
            } else {
              $("#AjaxContent").hide();
              $("#AjaxContent").html(
                '<div class="alert alert-' +
                  JsonLostPassword.label +
                  '" role="alert">' +
                  JsonLostPassword.message +
                  "</div>"
              );
              $("#AjaxContent").fadeIn();
              $("#lost_form_email").removeAttr("style");
              $("#form_button_lost").removeAttr("style");
            }
          } catch (error) {
            //window.location = "";
          }
          RecaptchaG();
        }
      ).fail(() => {
        //window.location = "";
      });
    }, 1000);
  }
});

$("#EmailVerifyForm").on("submit", () => {
  $("#AjaxContent").html("");
  $("#verify_form_pin").blur();
  const VerifyPin = $("#verify_form_pin").val();

  if (VerifyPin != "") {
    $("#form_button_verify").attr("style", "pointer-events:none;opacity:0.5;");
    setTimeout(() => {
      $.post(
        InternalAjaxHost + "web-service/security/layer/register",
        $("#EmailVerifyForm").serialize(),
        (data) => {
          try {
            const JsonVerifyPin = JSON.parse(data);

            if (JsonVerifyPin.status == "success") {
              $("#AjaxContent").hide();
              $("#AjaxContent").html(
                '<div class="alert alert-' +
                  JsonVerifyPin.label +
                  '" role="alert">' +
                  JsonVerifyPin.message +
                  "</div>"
              );
              $("#AjaxContent").fadeIn();
              $("#lost_form_email").removeAttr("style");
              $("#form_button_lost").removeAttr("style");

              window.location =
                InternalAjaxHost + "go?href=" + $.urlParam("next");
            } else {
              $("#AjaxContent").hide();
              $("#AjaxContent").html(
                '<div class="alert alert-' +
                  JsonVerifyPin.label +
                  '" role="alert">' +
                  JsonVerifyPin.message +
                  "</div>"
              );
              $("#AjaxContent").fadeIn();
              $("#form_button_verify").removeAttr("style");
            }
          } catch (error) {
            //window.location = "";
          }
          RecaptchaG();
        }
      ).fail(() => {
        //window.location = "";
      });
    }, 1000);
  }
});

$("#NwCode").on("click", () => {
  $("#AjaxContent").html("");
  $("#verify_form_pin").blur();
  $.post(
    "/web-service/pin",
    {
      layer: 2,
    },
    (data) => {
      try {
        const JsonVerifyPinD = JSON.parse(data);

        if (JsonVerifyPinD.status == "success") {
          $("#AjaxContent").hide();
          $("#AjaxContent").html(
            '<div class="alert alert-' +
              JsonVerifyPinD.label +
              '" role="alert">' +
              JsonVerifyPinD.message +
              "</div>"
          );
          $("#AjaxContent").fadeIn();
        } else {
          $("#AjaxContent").hide();
          $("#AjaxContent").html(
            '<div class="alert alert-' +
              JsonVerifyPinD.label +
              '" role="alert">' +
              JsonVerifyPinD.message +
              "</div>"
          );
          $("#AjaxContent").fadeIn();
        }
      } catch (error) {
        //window.location = "";
      }
    }
  ).fail(() => {
    //window.location = "";
  });
});

$("#RegisterButtonTop").on("click", (e) => {
  e.preventDefault();
  $("#LoginButtonTop").removeClass("active");
  $("#RegisterButtonTop").addClass("active");
  $("#AjaxContent").html("");

  $("#LoginForm").hide();
  $("#LostPwForm").hide();
  $("#EmailVerifyForm").hide();
  $("#RegisterForm").slideDown();

  $("#H11").hide();
  $("#H12").fadeIn();

  return false;
});

$("#LoginButtonTop").on("click", (e) => {
  e.preventDefault();
  $("#RegisterButtonTop").removeClass("active");
  $("#LoginButtonTop").addClass("active");
  $("#AjaxContent").html("");

  $("#RegisterForm").hide();
  $("#LostPwForm").hide();
  $("#EmailVerifyForm").hide();
  $("#LoginForm").slideDown();

  $("#H12").hide();
  $("#H11").fadeIn();

  return false;
});

$("#LostPassword").on("click", () => {
  $("#AjaxContent").html("");
  $("#RegisterForm").hide();
  $("#LoginForm").hide();
  $("#EmailVerifyForm").hide();
  $("#LostPwForm").slideDown();
});

$("#Back2Login").on("click", () => {
  $("#AjaxContent").html("");
  $("#RegisterForm").hide();
  $("#LostPwForm").hide();
  $("#EmailVerifyForm").hide();
  $("#LoginForm").slideDown();
});

$("#RegisterForm").on("submit", () => {
  $("#AjaxContent").html("");
  let Name = $("#RandomInput1").val();
  let Email = $("#RandomInput2").val();
  let Password = $("#RandomInput3").val();
  $("#RandomInput3").blur();

  if (Name != "" && Email != "" && Password != "") {
    $("#RandomInput1").attr("style", "pointer-events:none;opacity:0.5;");
    $("#RandomInput2").attr("style", "pointer-events:none;opacity:0.5;");
    $("#RandomInput3").attr("style", "pointer-events:none;opacity:0.5;");
    $("#register_button").attr("style", "pointer-events:none;opacity:0.5;");
    setTimeout(() => {
      $.post(
        InternalAjaxHost + "web-service/register",
        $("#RegisterForm").serialize(),
        (data) => {
          try {
            let JsonLogin = JSON.parse(data);

            if (JsonLogin.status == "success") {
              $("#AjaxContent").hide();
              $("#AjaxContent").html(
                '<div class="alert alert-' +
                  JsonLogin.label +
                  '" role="alert">' +
                  JsonLogin.message +
                  "</div>"
              );
              $("#AjaxContent").fadeIn();
              $("#RandomInput1").removeAttr("style");
              $("#RandomInput2").removeAttr("style");
              $("#RandomInput3").removeAttr("style");
              $("#register_button").removeAttr("style");
              $("#RandomInput1").attr("disabled", "disabled");
              $("#RandomInput2").attr("disabled", "disabled");
              $("#register_button").attr("disabled", "disabled");

              $("#RegisterForm").hide();
              $("#LostPwForm").hide();
              $("#LoginForm").hide();
              $("#EmailVerifyForm").slideDown();
              $("#EmailVerifyForm").focus();
            } else {
              $("#AjaxContent").hide();
              $("#AjaxContent").html(
                '<div class="alert alert-' +
                  JsonLogin.label +
                  '" role="alert">' +
                  JsonLogin.message +
                  "</div>"
              );
              $("#AjaxContent").fadeIn();
              $("#RandomInput3").val("");
              $("#RandomInput1").removeAttr("style");
              $("#RandomInput2").removeAttr("style");
              $("#RandomInput3").removeAttr("style");
              $("#register_button").removeAttr("style");
            }
          } catch (error) {
            //window.location = "";
          }
          RecaptchaG();
        }
      ).fail(() => {
        //window.location = "";
      });
    }, 1000);
  }
});

const LoginWith = (data) => {
  const url = InternalAjaxHost + "social-login/with/" + data;

  const w = 770;
  const h = 650;
  const y = window.top.outerHeight / 2 + window.top.screenY - h / 2;
  const x = window.top.outerWidth / 2 + window.top.screenX - w / 2 - 200;

  const newwin = window.open(
    url,
    SomeText[4],
    "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=" +
      w +
      ", height=" +
      h +
      ", top=" +
      y +
      ", left=" +
      x
  );
  if (window.focus) {
    newwin.focus();
  }

  const timer = setInterval(function () {
    if (newwin.closed) {
      clearInterval(timer);
      $.post(
        InternalAjaxHost + "web-service/check/session",
        { source: data },
        (data) => {
          try {
            const JsonData = JSON.parse(data);

            if (JsonData.isLogged == true) {
              $("#AjaxContent").hide();
              $("#AjaxContent").html(
                '<div class="alert alert-' +
                  JsonData.label +
                  '" role="alert">' +
                  JsonData.message +
                  "</div>"
              );
              $("#AjaxContent").fadeIn();
              $("#form_email").removeAttr("style");
              $("#form_password").removeAttr("style");
              $("#form_button").removeAttr("style");
              $("#form_email").attr("disabled", "disabled");
              $("#form_button").attr("disabled", "disabled");
              window.location =
                InternalAjaxHost + "go?href=" + $.urlParam("next");
            } else {
              $("#AjaxContent").hide();
              $("#AjaxContent").html(
                '<div class="alert alert-' +
                  JsonData.label +
                  '" role="alert">' +
                  JsonData.message +
                  "</div>"
              );
              $("#AjaxContent").fadeIn();
              $("#form_email").removeAttr("style");
              $("#form_password").removeAttr("style");
              $("#form_button").removeAttr("style");
            }
          } catch (error) {
            //window.location = "";
          }
        }
      ).fail(() => {
        //window.location = "";
      });
    }
  }, 1000);
};
