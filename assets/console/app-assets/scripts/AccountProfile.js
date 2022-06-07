var t2StepRefresh = () => {
  $("#2step_loading").show();
  $("#2step_area").attr("style", "pointer-events:none;opacity:0.5;");
  $(".2step_checkbox").attr("disabled", "disabled");
  setTimeout(() => {
    var channels = {
      active: false,
      email: false,
      sms: false,
      google: false,
      notification: false,
    };

    if ($("#p_switch1").is(":checked")) channels.active = true;
    if ($("#p_switch2").is(":checked")) channels.email = true;
    if ($("#p_switch3").is(":checked")) channels.sms = true;
    if ($("#p_switch4").is(":checked")) channels.google = true;
    if ($("#p_switch5").is(":checked")) channels.notification = true;

    $.post(
      InternalAjaxHost + "web-service/2step/prefences",
      { channels },
      (j) => {
        try {
          var JsnD = JSON.parse(j);

          if (JsnD.PinValidate == "required") {
            PinVerify(JsnD.PinInfo);
          } else {
            $("#t2step_text").text(JsnD.t2step.isActive.text);
            $(".2step_checkbox").removeAttr("disabled");
            $(".2step_checkbox").prop("checked", false);

            if (JsnD.t2step.isActive.active == true) {
              $("#p_switch1").prop("checked", true);
              if (JsnD.t2step.channels.email == true) {
                $("#p_switch2").attr("disabled", "disabled");
                $("#p_switch2").prop("checked", true);
              }
              if (JsnD.t2step.channels.sms == true) {
                $("#p_switch3").attr("disabled", "disabled");
                $("#p_switch3").prop("checked", true);
              }
              if (JsnD.t2step.channels.google == true) {
                $("#p_switch4").prop("checked", true);
              }
              if (JsnD.t2step.channels.notification == true) {
                $("#p_switch5").prop("checked", true);
              }

              if (JsnD.t2step.modals.google == true) {
                GoogleAuthModal();
              }

              if (JsnD.t2step.modals.notification == true) {
                NotificationAccountSelect();
              }
            } else {
              $(".2step_checkbox").prop("checked", false);
              $(".2step_channels").attr("disabled", "disabled");
            }

            $("#2step_loading").hide();
            $("#2step_area").removeAttr("style");
          }
        } catch (error) {
          AjaxFail();
        }
      }
    ).fail(() => {
      AjaxFail();
    });
  }, 700);
};

var SocialBlockRefresh = (isTopButton = false) => {
  $("#social_banned_loading").show();
  $("#social_banned_area").attr("style", "pointer-events:none;opacity:0.5;");
  $(".social_banned_checkbox").attr("disabled", "disabled");
  setTimeout(() => {
    var channels = {
      topButton: isTopButton,
      active: false,
      google: false,
      github: false,
      linkedin: false,
      facebook: false,
    };

    if ($("#s_switch0").is(":checked")) channels.active = true;
    if ($("#s_switch1").is(":checked")) channels.google = true;
    if ($("#s_switch2").is(":checked")) channels.github = true;
    if ($("#s_switch3").is(":checked")) channels.linkedin = true;
    if ($("#s_switch4").is(":checked")) channels.facebook = true;

    $.post(
      InternalAjaxHost + "web-service/social/login/prefences",
      { channels },
      (j) => {
        try {
          var JsnD = JSON.parse(j);

          $(".social_banned_checkbox").removeAttr("disabled");
          $(".social_banned_checkbox").prop("checked", false);

          if (JsnD.socialLoginActive == true) {
            $("#s_switch0").prop("checked", true);
          } else {
            $(".social_checknox2").attr("disabled", "disabled");
          }

          $("#s_switch0_text").text(JsnD.socialLoginLabel);

          if (JsnD.bannedSocials.Google.isActive == true) {
            $("#s_switch1").prop("checked", true);
          }

          if (JsnD.bannedSocials.Github.isActive == true) {
            $("#s_switch2").prop("checked", true);
          }

          if (JsnD.bannedSocials.Linkedin.isActive == true) {
            $("#s_switch3").prop("checked", true);
          }

          if (JsnD.bannedSocials.Facebook.isActive == true) {
            $("#s_switch4").prop("checked", true);
          }

          $("#social_banned_loading").hide();
          $("#social_banned_area").removeAttr("style");
        } catch (error) {
          AjaxFail();
        }
      }
    ).fail(() => {
      AjaxFail();
    });
  }, 700);
};

var RefreshNotifQr = () => {
  $.post(
    InternalAjaxHost + "web-service/refresh/notification/qr",
    { refresh: "qr" },
    (qrc) => {
      $("#NtfSuccessGif").attr("src", qrc);
    }
  );
};

var NotificationAccountSelect = () => {
  $("#NtfSuccessGif").attr(
    "src",
    InternalAjaxHost + "assets/media/img_loading.gif"
  );
  $("#NtfFooterArea").removeAttr("style");
  $("#NtfFooterArea").hide();
  $(".pincode-input-text").val("");
  $("#NtfModal").modal("toggle");
  $.post(
    InternalAjaxHost + "web-service/notification/authenticator",
    { load: "modal" },
    (j) => {
      try {
        var JsnG = JSON.parse(j);

        $("#NtfFooterArea").show();
        $(".Ntf_footer_k").show();
        setTimeout(() => {
          $("#Ntf-pincode-verify").data("plugin_pincodeInput").focus();
        }, 500);

        if (JsnG.showModal == true) {
          $("#NtfSuccessGif").attr("src", JsnG.QrCodeUrl);

          var RefreshQrInterval = setInterval(() => {
            if ($("#NtfModal").is(":visible")) {
              RefreshNotifQr();
            } else {
              clearInterval(RefreshQrInterval);
            }
          }, 60000);
        } else {
          if (JsnG.ShowMessage == true) {
            toastr.warning(JsnG.Message, JsnG.Title, {
              closeButton: true,
              timeOut: 8000,
            });
          }
        }
      } catch (error) {
        AjaxFail();
      }
    }
  ).fail(() => {
    AjaxFail();
  });
};

var GoogleAuthModal = () => {
  $("#GoogleSuccessGif").attr(
    "src",
    InternalAjaxHost + "assets/media/img_loading.gif"
  );
  $("#GoogleFooterArea").removeAttr("style");
  $("#GoogleFooterArea").hide();
  $(".google_footer_k").show();
  $("#GoogleSecretKey").text("");
  $(".pincode-input-text").val("");
  $("#GoogleModal").modal("toggle");
  $.post(
    InternalAjaxHost + "web-service/google/authenticator",
    { load: "modal" },
    (j) => {
      try {
        var JsnG = JSON.parse(j);

        $("#GoogleFooterArea").show();
        setTimeout(() => {
          $("#google-pincode-verify").data("plugin_pincodeInput").focus();
        }, 500);

        if (JsnG.showModal == true) {
          $("#GoogleSuccessGif").attr("src", JsnG.QrCodeUrl);
          $("#GoogleSecretKey").text(JsnG.SecretKey);
        } else {
          if (JsnG.ShowMessage == true) {
            toastr.warning(JsnG.Message, JsnG.Title, {
              closeButton: true,
              timeOut: 8000,
            });
          }
        }
      } catch (error) {
        AjaxFail();
      }
    }
  ).fail(() => {
    AjaxFail();
  });
};

var FastRefresh2Step = () => {
  $.post(
    InternalAjaxHost + "web-service/my/profile",
    { load: "profile" },
    (j) => {
      try {
        var JsnD = JSON.parse(j);

        $("#t2step_text").text(JsnD.t2step.isActive.text);
        $(".2step_checkbox").removeAttr("disabled");
        $(".2step_checkbox").prop("checked", false);
        if (JsnD.t2step.isActive.active == true) {
          $("#p_switch1").prop("checked", true);
          if (JsnD.t2step.channels.email == true) {
            $("#p_switch2").attr("disabled", "disabled");
            $("#p_switch2").prop("checked", true);
          }
          if (JsnD.t2step.channels.sms == true) {
            $("#p_switch3").attr("disabled", "disabled");
            $("#p_switch3").prop("checked", true);
          }
          if (JsnD.t2step.channels.google == true) {
            $("#p_switch4").prop("checked", true);
          }
          if (JsnD.t2step.channels.notification == true) {
            $("#p_switch5").prop("checked", true);
          }
        } else {
          $(".2step_checkbox").prop("checked", false);
          $(".2step_channels").attr("disabled", "disabled");
        }

        $("#2step_loading").hide();
        $("#2step_area").removeAttr("style");
      } catch (error) {
        AjaxFail();
      }
    }
  ).fail(() => {
    AjaxFail();
  });
};

$.post(
  InternalAjaxHost + "web-service/my/profile",
  { load: "profile" },
  (j) => {
    try {
      var JsnD = JSON.parse(j);

      setTimeout(() => {
        $("#AvatarSrc").attr("src", JsnD.avatar);
      }, 500);

      $.each(JsnD.profile, (index, element) => {
        $('[data-key="' + index + '"]').html(element);
      });

      $("#t2step_text").text(JsnD.t2step.isActive.text);
      $(".2step_checkbox").removeAttr("disabled");
      $(".2step_checkbox").prop("checked", false);
      if (JsnD.t2step.isActive.active == true) {
        $("#p_switch1").prop("checked", true);
        if (JsnD.t2step.channels.email == true) {
          $("#p_switch2").attr("disabled", "disabled");
          $("#p_switch2").prop("checked", true);
        }
        if (JsnD.t2step.channels.sms == true) {
          $("#p_switch3").attr("disabled", "disabled");
          $("#p_switch3").prop("checked", true);
        }
        if (JsnD.t2step.channels.google == true) {
          $("#p_switch4").prop("checked", true);
        }
        if (JsnD.t2step.channels.notification == true) {
          $("#p_switch5").prop("checked", true);
        }
      } else {
        $(".2step_checkbox").prop("checked", false);
        $(".2step_channels").attr("disabled", "disabled");
      }

      $(".social_banned_checkbox").removeAttr("disabled");
      $(".social_banned_checkbox").prop("checked", false);

      if (JsnD.socialLoginActive == true) {
        $("#s_switch0").prop("checked", true);
      } else {
        $(".social_checknox2").attr("disabled", "disabled");
      }

      $("#s_switch0_text").text(JsnD.socialLoginLabel);

      if (JsnD.bannedSocials.Google.isActive == true) {
        $("#s_switch1").prop("checked", true);
      }

      if (JsnD.bannedSocials.Github.isActive == true) {
        $("#s_switch2").prop("checked", true);
      }

      if (JsnD.bannedSocials.Linkedin.isActive == true) {
        $("#s_switch3").prop("checked", true);
      }

      if (JsnD.bannedSocials.Facebook.isActive == true) {
        $("#s_switch4").prop("checked", true);
      }
    } catch (error) {
      AjaxFail();
    }
  }
).fail(() => {
  AjaxFail();
});

$("#google-pincode-verify")
  .pincodeInput({
    inputs: 6,
    complete: function (value, e, errorElement) {
      $(".pincode-input-text").blur();
      $("#GoogleFooterArea").attr("style", "pointer-events:none;opacity:0.5");
      $.post(
        InternalAjaxHost + "web-service/google/authenticator/verify",
        { pin: value },
        (jn) => {
          try {
            const JsR = JSON.parse(jn);
            if (JsR.process == "success") {
              setTimeout(() => {
                toastr.success(JsR.message, JsR.title, {
                  closeButton: true,
                  timeOut: 8000,
                });

                $(".pincode-input-text").val("");
                $("#GoogleFooterArea").hide();
                $(".google_footer_k").hide();
                var OldText = $("#GoogleSuccessText").text();
                setTimeout(() => {
                  $("#GoogleSuccessText").text(OldText);
                }, 3000);
                setTimeout(() => {
                  $("#GoogleModal").modal("toggle");

                  if (JsR.callbackJs != "") {
                    setTimeout(() => {
                      eval(JsR.callbackJs + "()");
                    }, 500);
                  }
                }, 2000);
                $("#GoogleSuccessText").text(JsR.message);
                $("#GoogleSuccessGif").attr(
                  "src",
                  InternalAjaxHost + "assets/media/success.gif"
                );
                $(".pincode-input-text").val("");
              }, 1500);
            } else {
              setTimeout(() => {
                toastr.warning(JsR.message, JsR.title, {
                  closeButton: true,
                  timeOut: 8000,
                });
                $("#google-pincode-verify").data("plugin_pincodeInput").focus();
                $(".pincode-input-text").val("");
                $("#GoogleFooterArea").removeAttr("style");
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

$("#Ntf-pincode-verify")
  .pincodeInput({
    inputs: 6,
    complete: function (value, e, errorElement) {
      $(".pincode-input-text").blur();
      $("#NtfFooterArea").attr("style", "pointer-events:none;opacity:0.5");
      $.post(
        InternalAjaxHost + "web-service/notification/authenticator/verify",
        { pin: value },
        (jn) => {
          try {
            const JsR = JSON.parse(jn);
            if (JsR.process == "success") {
              setTimeout(() => {
                toastr.success(JsR.message, JsR.title, {
                  closeButton: true,
                  timeOut: 8000,
                });

                $(".pincode-input-text").val("");
                $("#NtfFooterArea").hide();
                $(".Ntf_footer_k").hide();
                var OldText = $("#NtfSuccessText").text();
                setTimeout(() => {
                  $("#NtfSuccessText").text(OldText);
                }, 3000);
                setTimeout(() => {
                  $("#NtfModal").modal("toggle");

                  if (JsR.callbackJs != "") {
                    setTimeout(() => {
                      eval(JsR.callbackJs + "()");
                    }, 500);
                  }
                }, 2000);
                $("#NtfSuccessText").text(JsR.message);
                $("#NtfSuccessGif").attr(
                  "src",
                  InternalAjaxHost + "assets/media/success.gif"
                );
                $(".pincode-input-text").val("");
              }, 1500);
            } else {
              setTimeout(() => {
                toastr.warning(JsR.message, JsR.title, {
                  closeButton: true,
                  timeOut: 8000,
                });
                $("#Ntf-pincode-verify").data("plugin_pincodeInput").focus();
                $(".pincode-input-text").val("");
                $("#NtfFooterArea").removeAttr("style");
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
