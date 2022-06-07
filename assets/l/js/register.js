$("#RegisterForm").on("submit", () => {
  $("#RegisterAjaxContent").html("");
  let Name = $("#RandomInput1").val();
  let Email = $("#RandomInput2").val();
  let Password = $("#RandomInput3").val();

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
              $("#RegisterAjaxContent").hide();
              $("#RegisterAjaxContent").html(
                '<div class="alert alert-' +
                  JsonLogin.label +
                  '" role="alert">' +
                  JsonLogin.message +
                  "</div>"
              );
              $("#RegisterAjaxContent").fadeIn();
              $("#RandomInput1").removeAttr("style");
              $("#RandomInput2").removeAttr("style");
              $("#RandomInput3").removeAttr("style");
              $("#register_button").removeAttr("style");
              $("#RandomInput1").attr("disabled", "disabled");
              $("#RandomInput2").attr("disabled", "disabled");
              $("#register_button").attr("disabled", "disabled");
              window.location =
                InternalAjaxHost + "go?href=" + $.urlParam("href");
            } else {
              $("#RegisterAjaxContent").hide();
              $("#RegisterAjaxContent").html(
                '<div class="alert alert-' +
                  JsonLogin.label +
                  '" role="alert">' +
                  JsonLogin.message +
                  "</div>"
              );
              $("#RegisterAjaxContent").fadeIn();
              $("#RandomInput3").val("");
              $("#RandomInput1").removeAttr("style");
              $("#RandomInput2").removeAttr("style");
              $("#RandomInput3").removeAttr("style");
              $("#register_button").removeAttr("style");
            }
          } catch (error) {
            window.location = "";
          }
          RecaptchaG();
        }
      ).fail(() => {
        window.location = "";
      });
    }, 1000);
  }
});

let LoginWith = (data) => {
  let url = InternalAjaxHost + "social-login/with/" + data;

  let w = 770;
  let h = 650;
  let y = window.top.outerHeight / 2 + window.top.screenY - h / 2;
  let x = window.top.outerWidth / 2 + window.top.screenX - w / 2 - 200;

  let newwin = window.open(
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

  let timer = setInterval(function () {
    if (newwin.closed) {
      clearInterval(timer);
      $.post(
        InternalAjaxHost + "web-service/check/session",
        { source: data },
        (data) => {
          try {
            let JsonData = JSON.parse(data);

            if (JsonData.isLogged == true) {
              $("#RegisterAjaxContent").hide();
              $("#RegisterAjaxContent").html(
                '<div class="alert alert-' +
                  JsonData.label +
                  '" role="alert">' +
                  JsonData.message +
                  "</div>"
              );
              $("#RegisterAjaxContent").fadeIn();
              $("#RandomInput1").removeAttr("style");
              $("#RandomInput2").removeAttr("style");
              $("#RandomInput3").removeAttr("style");
              $("#register_button").removeAttr("style");
              $("#RandomInput1").attr("disabled", "disabled");
              $("#RandomInput2").attr("disabled", "disabled");
              $("#register_button").attr("disabled", "disabled");
              window.location =
                InternalAjaxHost + "go?href=" + $.urlParam("href");
            } else {
              $("#RegisterAjaxContent").hide();
              $("#RegisterAjaxContent").html(
                '<div class="alert alert-' +
                  JsonData.label +
                  '" role="alert">' +
                  JsonData.message +
                  "</div>"
              );
              $("#RegisterAjaxContent").fadeIn();
              $("#RandomInput1").removeAttr("style");
              $("#RandomInput2").removeAttr("style");
              $("#RandomInput3").removeAttr("style");
              $("#register_button").removeAttr("style");
            }
          } catch (error) {
            window.location = "";
          }
        }
      ).fail(() => {
        window.location = "";
      });
    }
  }, 1000);
};
