const loadJavascriptAndCssFiles = (urls, successCallback) => {
  $.when
    .apply(
      $,
      $.map(urls, function (url) {
        if (url.endsWith(".css")) {
          return $.get(url, function (css) {
            $("<style>" + css + "</style>").appendTo("head");
          });
        } else {
          return $.getScript(url);
        }
      })
    )
    .then(function () {
      if (typeof successCallback === "function") successCallback();
    })
    .fail(function () {
      if (typeof failureCallback === "function") successCallback();
    });
};

async function getScripts(scripts, callback) {
  var progress = 0;
  for (let index = 0; index < scripts.length; index++) {
    await $.getScript(scripts[index], function () {
      if (++progress == scripts.length) callback();
    });
  }
}

const PageLoaded = () => {
  $.getJSON("?__a=1", (js) => {
    getScripts(js.PageJs, () => {
      const Path = window.location.pathname;
      const split = Path.split("/");
      let lpath;
      let JsUrl = "";
      $.each(split, (i) => {
        lpath = split[i];
        if (lpath != "" && lpath.length < 20 && lpath != "console") {
          lpath = lpath.split("?")[0];
          lpath = lpath.toLowerCase().replace(/\b[a-z]/g, function (letter) {
            return letter.toUpperCase();
          });
          JsUrl = JsUrl + lpath;
        }
      });
      $("#SpinnerApp").hide();
      $(".MainContent").fadeIn();
      PageTitleEvent();
      setTimeout(() => {
        $.getScript(InternalAjaxHost + "console/script/" + JsUrl + ".js");
      }, 500);
    });
    loadJavascriptAndCssFiles(js.PageCss);

    if (js.AccountVerifyRequired == true) {
      $.post(
        InternalAjaxHost + "web-service/account/level",
        { is: "verified" },
        (jsd) => {
          try {
            const VerifyJson = JSON.parse(jsd);
            if (VerifyJson.isVerified == false) {
              barba.go("/console/account/verify");
            }
          } catch (error) {
            AjaxFail();
          }
        }
      ).fail(() => {
        AjaxFail();
      });
    }
  });
};
/// Barba Start
$(document).ready(function () {
  barba.init({
    cacheIgnore: false,
    schema: {
      prefix: "data-barba",
      namespace: "easy",
      wrapper: "wrapper",
    },
    transitions: [
      {
        name: "default-transition",
        leave() {
          $("#SpinnerApp").show();
        },
        before() {
          $("#SpinnerBreadCrumb").html("");
          if ($("#ConsoleBreadCrumb").length > 0) {
            const HtmlBreadCrumb = $("#ConsoleBreadCrumb").html();
            $("#SpinnerBreadCrumb").html(HtmlBreadCrumb);
          }
        },
        enter() {
          PageLoaded();
        },
      },
    ],
  });
});
/// Barba End

const NotifClick = (token) => {
  if (token == "verify") {
    AccountValidate();
  } else {
    $.post(
      InternalAjaxHost + "web-service/click/notification",
      { tkn: token },
      (url) => {
        CheckNotification();
        barba.go(url);
      }
    );
  }
};

const PageTitleEvent = () => {
  dontGo({
    title: "It's really Easy",
    timeout: 5000,
  });
};

const CheckIsAlive = () => {
  $("main").html(
    '    <h2 style="    z-index: 1;left: 300px;position: absolute;top: 120px;color: #626262;" class="content-header-title float-left mb-0">Your session has been terminated for security reasons. Please refresh the page and log in again.</h2>'
  );
};

const CheckNotification = () => {
  $.post(
    InternalAjaxHost + "web-service/notifications",
    { load: "notification" },
    (data) => {
      if (data == "SessionDestroyed") {
        CheckIsAlive();
      } else {
        try {
          const JsonData = JSON.parse(data);
          if (JsonData.NotificationCount == 0) {
            $("#Notif_Count").attr("style", "display:none;");
          } else {
            $("#Notif_Count").removeAttr("style");
          }
          $("#ReadAllNotif").text(JsonData.ButtonText);
          $("#Notif_Count").text(JsonData.NotificationCount);
          $("#Notif_Count2").text(JsonData.NotificationCountText);
          $("#Notif_List").html("");
          let SNotif;
          $.each(JsonData.Notifications, (notif) => {
            SNotif = JsonData.Notifications[notif];

            $("#Notif_List").append(
              '<a class="d-flex justify-content-between no-barba" onclick="NotifClick(\'' +
                SNotif.token +
                '\');" href="javascript:;">' +
                ' <div class="media d-flex align-items-start">' +
                '<div class="media-left"><i class="feather font-medium-5 ' +
                SNotif.label.type +
                " " +
                SNotif.label.icon +
                '"></i></div>' +
                ' <div class="media-body">' +
                ' <h6 class="' +
                SNotif.label.type +
                ' media-heading">' +
                SNotif.title +
                '</h6><small class="notification-text">' +
                SNotif.text +
                "</small>" +
                " </div><small>" +
                '<time class="media-meta" >' +
                SNotif.time +
                "</time></small>" +
                " </div>" +
                " </a>"
            );
          });

          if (JsonData.FailedLogin > 0) {
            setTimeout(() => {
              toastr.error(
                JsonData.FailedLoginTexts.text,
                JsonData.FailedLoginTexts.label,
                {
                  closeButton: true,
                  timeOut: 15000,
                  onclick: () => {
                    barba.go("/console/security/login");
                  },
                }
              );
            }, 2000);
          }
        } catch (error) {
          console.log("Notifications could not be loaded.");
        }
      }
    }
  ).fail((data) => {
    try {
      const ErrorJson = JSON.parse(data.responseText);
      if (ErrorJson.HttpStatusCode == 403) {
        CheckIsAlive();
      }
    } catch (error) {
      console.log("Notifications could not be loaded");
    }
  });
};

const ChangeThemeMode = () => {
  let LastDefined;
  if (AppMode == undefined) {
    LastDefined = "Light";
    $("#Btn_LightMode").show();
    $("body").removeClass("dark-layout");
  } else {
    if (AppMode == "Light") {
      LastDefined = "Dark";
      $("#Btn_DarkMode").show();
      $("#Btn_LightMode").hide();
      $("body").addClass("dark-layout");
    } else if (AppMode == "Dark") {
      LastDefined = "Light";
      $("#Btn_LightMode").show();
      $("#Btn_DarkMode").hide();
      $("body").removeClass("dark-layout");
    } else {
      LastDefined = "Light";
      $("#Btn_LightMode").show();
      $("body").removeClass("dark-layout");
    }
  }

  AppMode = LastDefined;

  $.post(
    InternalAjaxHost + "web-service/app/mode",
    { mode: LastDefined },
    (data) => {}
  );
};

const MenuType = () => {
  $.post(
    InternalAjaxHost + "web-service/app/menu",
    { data: "data" },
    (data) => {}
  );
};

const AjaxFail = () => {
  toastr.error(
    "There was a problem processing the request. Please try again later.",
    "Something were wrong!",
    {
      closeButton: true,
      timeOut: 5000,
    }
  );
};

const PinVerify = (JsObject) => {
  $("#PinVerifySuccessGif").attr(
    "src",
    InternalAjaxHost + "assets/media/phone_verify.png"
  );
  $("#PinVerifyFooterArea").removeAttr("style");
  $("#PinVerifyFooterArea").hide();
  $("#PinVerifyFooterAreaLoading").show();
  $("#PinVerifyModal").modal("toggle");
  setTimeout(() => {
    $("#PinSendedInfo").text(JsObject.PinSendedTo);
    $("#PinVerifyFooterAreaLoading").hide();
    $("#PinVerifyFooterArea").show();
    setTimeout(() => {
      $("#pincode-verify").data("plugin_pincodeInput").focus();
    }, 500);
  }, 1000);
};

const SubmitForm = (FormID, AjaxSource, callback) => {
  $("#" + FormID + " input,select,textarea")
    .not("[type=submit]")
    .jqBootstrapValidation({
      preventSubmit: true,
      submitSuccess: function ($form, event) {
        event.preventDefault();
        let SelectForm = $("#" + FormID);
        $("input, textarea").trigger("blur");
        SelectForm.attr("style", "pointer-events:none;opacity:0.5");
        let FormData = $("#" + FormID).serialize();
        setTimeout(() => {
          $.ajax({
            url: InternalAjaxHost + "web-service/" + AjaxSource,
            method: "POST",
            data: FormData,
            success: function (j) {
              try {
                let JsonFormData = JSON.parse(j);

                if (JsonFormData.showToastr != false) {
                  if (JsonFormData.status == "success") {
                    toastr.success(JsonFormData.message, JsonFormData.title, {
                      closeButton: true,
                      timeOut: 8000,
                    });
                  } else {
                    toastr.warning(JsonFormData.message, JsonFormData.title, {
                      closeButton: true,
                      timeOut: 8000,
                    });
                  }
                }

                callback(JsonFormData);
              } catch (error) {
                AjaxFail();
              }
            },
            error: function () {
              AjaxFail();
            },
            complete: function () {
              SelectForm.removeAttr("style");
            },
          });
        }, 500);
      },
    });
};

const FileUpload = (FormID, before, callback) => {
  topbar.show();
  before();
  $.ajax({
    url: InternalAjaxHost + "web-service/upload/file",
    type: "POST",
    data: new FormData($("#" + FormID).last()[0]),
    contentType: false,
    cache: false,
    processData: false,
    success: function (data) {
      try {
        const UploadJsn = JSON.parse(data);
        if (UploadJsn.status == "success") {
          if (UploadJsn.totalUploadedCount > 0) {
            toastr.success(UploadJsn.message, UploadJsn.title, {
              closeButton: true,
              timeOut: 8000,
            });
          }
          callback(UploadJsn);
        } else {
          toastr.warning(UploadJsn.message, UploadJsn.title, {
            closeButton: true,
            timeOut: 8000,
          });
        }

        $.each(UploadJsn.errorMessages, (index, element) => {
          toastr.warning(element, UploadJsn.errorTitle, {
            closeButton: true,
            timeOut: 8000,
          });
        });
      } catch (error) {
        AjaxFail();
      }

      setTimeout(() => {
        topbar.hide();
      }, 500);
    },
  }).fail(() => {
    AjaxFail();
    setTimeout(() => {
      topbar.hide();
    }, 500);
  });
};

$("#ReadAllNotif").on("click", (e) => {
  e.preventDefault();
  $("#Notif_Area").attr("style", "opacity:0.5;pointer-events:none");
  setTimeout(() => {
    $.post(
      InternalAjaxHost + "web-service/read/all/notification",
      { read: "all" },
      (data) => {
        $("#Notif_Area").removeAttr("style");
        CheckNotification();
      }
    );
  }, 1000);
  return false;
});

$("#LightOrDark").on("click", (e) => {
  e.preventDefault();
  return false;
});

// Pin code verify ready.
$("#pincode-verify")
  .pincodeInput({
    inputs: 6,
    complete: function (value, e, errorElement) {
      $(".pincode-input-text").blur();
      $("#PinVerifyFooterArea").attr(
        "style",
        "pointer-events:none;opacity:0.5"
      );
      $.post(
        InternalAjaxHost + "web-service/security/pin/verify",
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

                if ($("main").attr("data-barba-easy") == "validaterequired") {
                  barba.go("");
                }
                $(".pincode-input-text").val("");
                $("#PinVerifyFooterArea").hide();
                var OldText = $("#PinVerifySuccessText").text();
                setTimeout(() => {
                  $("#PinVerifySuccessText").text(OldText);
                }, 3000);
                setTimeout(() => {
                  $("#PinVerifyModal").modal("toggle");

                  if (JsR.callbackJs != "") {
                    setTimeout(() => {
                      eval(JsR.callbackJs + "()");
                    }, 500);
                  }
                }, 2000);
                $("#PinVerifySuccessText").text(JsR.message);
                $("#PinVerifySuccessGif").attr(
                  "src",
                  InternalAjaxHost + "assets/media/success.gif"
                );
                $(".pincode-input-text").val("");
                //$("#PinVerifyFooterArea").removeAttr("style");
              }, 1500);
            } else {
              setTimeout(() => {
                toastr.warning(JsR.message, JsR.title, {
                  closeButton: true,
                  timeOut: 8000,
                });
                $("#pincode-verify").data("plugin_pincodeInput").focus();
                $(".pincode-input-text").val("");
                $("#PinVerifyFooterArea").removeAttr("style");
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

//Page Loaded.
CheckNotification();
PageLoaded();
setInterval(() => {
  CheckNotification();
}, 30000);

$("#Btn_LightMode").on("click", (e) => {
  ChangeThemeMode();
});
$("#Btn_DarkMode").on("click", (e) => {
  ChangeThemeMode();
});

const onTidioChatApiReady = () => {
  document.tidioChatLang = document.querySelector("html").getAttribute("lang");
  $.post(
    InternalAjaxHost + "web-service/live/chat/user",
    { load: "user" },
    (data) => {
      const JsonChat = JSON.parse(data);
      tidioChatApi.setVisitorData({
        email: JsonChat.userEmail,
        distinct_id: JsonChat.userID,
        name: JsonChat.userName,
      });
    }
  );
};
if (window.tidioChatApi) {
  window.tidioChatApi.on("ready", onTidioChatApiReady);
} else {
  document.addEventListener("tidioChat-ready", onTidioChatApiReady);
}
