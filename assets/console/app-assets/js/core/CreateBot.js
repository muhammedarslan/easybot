$(".select2").select2({
  dropdownAutoWidth: true,
  width: "100%",
});

var SaveAndGo = (url) => {
  $("#AddNewStepModal").modal("hide");
  var Path = window.location.pathname;
  var split = Path.split("/");
  var data = {
    urlAddress: url,
    botName: $("#floating-label1").val(),
    botCategories: $("#floating-label2").val(),
    urlToken: split[4],
  };
  $.post(InternalAjaxHost + "web-service/create/bot/save", data, (jsn) => {
    try {
      var JsnD = JSON.parse(jsn);
      barba.go(
        InternalAjaxHost +
          "console/create/bot/" +
          JsnD.token +
          "/87654" +
          JsnD.url
      );
    } catch (error) {
      AjaxFail();
    }
  }).fail(() => {
    AjaxFail();
  });
};

var NewStep = () => {
  $("#AddNewStepModal").modal("toggle");
};
