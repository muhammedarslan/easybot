const AccountValidate = () => {
  $("#ValidateModal").modal("toggle");
};

const WrongNumber = () => {
  $("#ValidateButton").removeClass("m-progress");
  $("#ValidateButton").show();
  $("#AccountValidateArea").removeAttr("style");
  $("#PinVerifyArea").hide();

  $("#AccountValidateArea").show();
  $("#PhoneN").text("");
  $(".pincode-input-text").val("");
};

const RePin = () => {
  $("#ValidateButton").removeClass("m-progress");
  $("#ValidateButton").show();
  $("#PinVerifyArea").hide();
  $("#AccountValidateArea").removeAttr("style");
  SubmitValidateForm();
};

setTimeout(() => {
  var input = document.querySelector("#phone");
  var iti = window.intlTelInput(input, {
    initialCountry: "auto",
    separateDialCode: true,
    geoIpLookup: function (callback) {
      $.post(
        InternalAjaxHost + "web-service/get/country/code",
        { load: "country" },
        (countryCode) => {
          callback(countryCode);
        }
      );
    },
    utilsScript: "/assets/console/app-assets/js/tel.util.js",
  });

  window.iti = iti;
}, 1000);

const SubmitValidateForm = (PhnNumberD = null) => {
  let PhoneNumber;
  if (PhnNumberD == null) {
    PhoneNumber = iti.getNumber(intlTelInputUtils.numberFormat.E164);
  } else {
    PhoneNumber = PhnNumberD;
  }

  if (
    !PhoneNumber.includes("_") &&
    PhoneNumber != "" &&
    PhoneNumber != "undefined"
  ) {
    $("#phone").blur();
    $("#ValidateButton").addClass("m-progress");
    $("#AccountValidateArea").attr("style", "pointer-events:none;opacity:0.5");
    setTimeout(() => {
      $.post(
        InternalAjaxHost + "web-service/account/verify",
        { phoneNumber: PhoneNumber },
        (j) => {
          try {
            const JsResponse = JSON.parse(j);

            if (JsResponse.process == "success") {
              toastr.success(JsResponse.message, JsResponse.title, {
                closeButton: true,
                timeOut: 8000,
              });

              //$("#ValidateButton").removeClass("m-progress");
              $("#ValidateButton").hide();
              $("#AccountValidateArea").hide();
              $("#PinVerifyArea").show();
              $("#PhoneN").text(JsResponse.PhoneNumber);

              $("#pincode-input1")
                .pincodeInput({
                  inputs: 6,
                  complete: function (value, e, errorElement) {
                    $(".pincode-input-text").blur();
                    $("#PinVerifyArea").attr(
                      "style",
                      "pointer-events:none;opacity:0.5"
                    );
                    $.post(
                      InternalAjaxHost + "web-service/account/verify/pin",
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

                              if (
                                $("main").attr("data-barba-easy") ==
                                "validaterequired"
                              ) {
                                barba.go("");
                              }

                              if (
                                $("main").attr("data-barba-easy") ==
                                "accountandprofileedit"
                              ) {
                                LoadProfileInfo();
                              }

                              CheckNotification();
                              $("#ValidateAccountForm").remove();
                              $("#PinVerifyArea").remove();
                              $("#SccText").text(JsR.message);
                              $("#SccGif").attr(
                                "src",
                                InternalAjaxHost + "assets/media/success.gif"
                              );
                              $(".pincode-input-text").val("");
                              $("#PinVerifyArea").removeAttr("style");
                            }, 1500);
                          } else {
                            setTimeout(() => {
                              toastr.warning(JsR.message, JsR.title, {
                                closeButton: true,
                                timeOut: 8000,
                              });
                              $("#pincode-input1")
                                .data("plugin_pincodeInput")
                                .focus();
                              $(".pincode-input-text").val("");
                              $("#PinVerifyArea").removeAttr("style");
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
                          WrongNumber();
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
                      WrongNumber();
                    });
                  },
                })
                .data("plugin_pincodeInput")
                .focus();
            } else {
              toastr.error(JsResponse.message, JsResponse.title, {
                closeButton: true,
                timeOut: 8000,
              });
              $("#ValidateButton").removeClass("m-progress");
              $("#AccountValidateArea").removeAttr("style");
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
            $("#ValidateButton").removeClass("m-progress");
            $("#AccountValidateArea").removeAttr("style");
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
        $("#ValidateButton").removeClass("m-progress");
        $("#AccountValidateArea").removeAttr("style");
      });
    }, 1000);
  }
};

$("#ValidateButton").on("click", () => {
  SubmitValidateForm();
  return false;
});

$("#ValidateAccountForm").on("submit", () => {
  SubmitValidateForm();
});
