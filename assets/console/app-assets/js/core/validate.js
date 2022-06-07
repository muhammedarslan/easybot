$.post("/web-service/validate/lang", { data: "data" }, (data) => {
  var SomeText = JSON.parse(data);
  Swal.fire({
    title: SomeText.Title,
    html: SomeText.Text,
    input: "text",
    confirmButtonClass: "btn btn-primary",
    buttonsStyling: false,
    inputAttributes: {
      autocapitalize: "off",
    },
    showCancelButton: true,
    allowOutsideClick: false,
    allowEscapeKey: false,
    confirmButtonText: SomeText.Button1,
    cancelButtonText: SomeText.Button2,
    showLoaderOnConfirm: true,
    cancelButtonClass: "btn btn-danger ml-1",
    preConfirm: function (login) {
      var Callback = "";

      if ($("main").attr("data-validate-callback") != undefined) {
        Callback = $("main").attr("data-validate-callback");
      }

      return $.post(
        "/web-service/validate/password",
        { password: login, callbackFunction: Callback },
        (data) => {
          var response = JSON.parse(data);
          if (response.status == "success") {
            return response.status;
          } else {
            return Swal.showValidationMessage(response.ErrorText);
          }
        }
      ).fail(() => {
        return Swal.showValidationMessage("Something were wrong.");
      });
    },
    allowOutsideClick: function () {
      !Swal.isLoading();
    },
  }).then(function (result) {
    if (result) {
      try {
        var json2 = JSON.parse(result.value);
        if (json2.status == "success") {
          if (json2.callbackJs != "") {
            setTimeout(() => {
              eval(json2.callbackJs + "()");
            }, 500);
            swal.close();
          } else {
            barba.go("");
          }
        } else {
          history.back();
          swal.close();
        }
      } catch (error) {
        history.back();
        swal.close();
      }
    } else {
      history.back();
      swal.close();
    }
  });
});
setTimeout(() => {
  $(".swal2-input").attr("type", "password");
  $(".swal2-input").attr("style", "text-align:center;");
}, 1000);
