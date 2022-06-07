var CreateBot = () => {
  $("#StartNowButton").addClass("disabled_");
  $.post(
    InternalAjaxHost + "web-service/create/bot/token",
    { create: "bot" },
    (j) => {
      try {
        var CrJ = JSON.parse(j);

        if (CrJ.status == "success") {
          barba.go("/console/create/bot/" + CrJ.token);
        } else if (CrJ.status == "verifyrequired") {
          barba.go("/console/account/verify");
        } else {
          AjaxFail();
        }
      } catch (error) {
        AjaxFail();
      }
    }
  ).fail(() => {
    AjaxFail();
  });
};
