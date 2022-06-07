var NewTicket = () => {
  $("#NewTicket").toggleClass("show");
  $("#MailFiles").html("");
  $("#helpInputTop").val("");
  $("#new_tc_mess").val("");
  $("#InputTicketFiles").val("");
  $('[name="new_ticket_label"]').val(2);

  $.post(InternalAjaxHost + "web-service/get/date", { get: "date" }, (d) => {
    var DateJson = JSON.parse(d);
    $("#NewTicketDate1").text(DateJson.time);
    $("#NewTicketDate2").text(DateJson.date);
  });
};

var GetTickets = (folderID) => {
  $.post(
    InternalAjaxHost + "web-service/get/tickets",
    { folder: folderID },
    (j) => {
      try {
        var Jsn = JSON.parse(j);
        var label;
        var itemClass;
        var favoriteClass;
        var OutputHtml = "";
        if (Jsn.itemsCount > 0) {
          $.each(Jsn.items, (key, item) => {
            label = "info";

            if (item.ticketFolder == 1) {
              label = "primary";
            }
            if (item.ticketFolder == 2) {
              label = "success";
            }
            if (item.ticketFolder == 3) {
              label = "warning";
            }

            itemClass = "";
            favoriteClass = "";

            if (item.isReaded) {
              itemClass = "mail-read";
            }

            if (item.isStarred) {
              favoriteClass = "warning";
            }

            OutputHtml +=
              '<li data-token="' +
              item.ticketToken +
              '" onclick="ShowTicket(' +
              "'" +
              item.ticketToken +
              "'" +
              ');" class="media ' +
              itemClass +
              '">' +
              '<div class="media-left pr-50">' +
              '<div class="avatar">' +
              ' <img src="' +
              item.user.avatar +
              '"  alt="avtar img holder">' +
              "  </div>" +
              ' <div class="user-action">' +
              '  <div class="vs-checkbox-con">' +
              '  <input type="checkbox">' +
              ' <span class="vs-checkbox vs-checkbox-sm">' +
              '   <span class="vs-checkbox--check">' +
              '     <i class="vs-icon feather icon-check"></i>' +
              " </span>" +
              "  </span>" +
              "</div>" +
              ' <span data-star-token="' +
              item.ticketToken +
              '" class="favorite ' +
              favoriteClass +
              '"><i class="feather icon-star"></i></span>' +
              " </div>" +
              " </div>" +
              ' <div class="media-body">' +
              '  <div class="user-details">' +
              ' <div class="mail-items">' +
              '  <h5 class="list-group-item-heading text-bold-600 mb-25">' +
              item.ticketSubject +
              "</h5>" +
              ' <span class="list-group-item-text text-truncate">' +
              item.user.writtenBy +
              "</span>" +
              "  </div>" +
              ' <div class="mail-meta-item">' +
              ' <span class="float-right">' +
              " <span" +
              ' class="mr-1 bullet bullet-' +
              label +
              ' bullet-sm"></span><span' +
              ' class="mail-date">' +
              item.ticketTime +
              "</span>" +
              " </span>" +
              " </div>" +
              " </div>" +
              ' <div class="mail-message">' +
              '  <p class="list-group-item-text truncate mb-0">' +
              item.ticketShortMessage +
              " </div>" +
              "</div>" +
              "</li>";
          });
          $(".email-user-list .no-results").removeClass("show");
          $("#TicketsArea").html(OutputHtml);
          setTimeout(() => {
            TicketsRenderHtml();
          }, 500);
        } else {
          $(".email-user-list .no-results").addClass("show");
          $("#TicketsArea").html(OutputHtml);
        }

        $("#TicketDetail").removeClass("show");
        $("#NewTicket").removeClass("show");

        $('[data-stat-key="folder1"]').text(Jsn.stats.solved);
        $('[data-stat-key="folder2"]').text(Jsn.stats.waitingReply);
        $('[data-stat-key="folder3"]').text(Jsn.stats.waitingProcess);
        $('[data-stat-key="folder4"]').text(Jsn.stats.avgReply);

        if (Jsn.stats.unReaded.solved > 0) {
          $('[data-stat-key="folderPill1"]').text(Jsn.stats.unReaded.solved);
        } else {
          $('[data-stat-key="folderPill1"]').text("");
        }

        if (Jsn.stats.unReaded.waitingReply > 0) {
          $('[data-stat-key="folderPill2"]').text(
            Jsn.stats.unReaded.waitingReply
          );
        } else {
          $('[data-stat-key="folderPill2"]').text("");
        }

        if (Jsn.stats.unReaded.waitingProcess > 0) {
          $('[data-stat-key="folderPill3"]').text(
            Jsn.stats.unReaded.waitingProcess
          );
        } else {
          $('[data-stat-key="folderPill3"]').text("");
        }
      } catch (error) {
        AjaxFail();
      }
    }
  ).fail(() => {
    AjaxFail();
  });
};

var UpdateTicketStats = () => {
  $.post(
    InternalAjaxHost + "web-service/ticket/stats",
    { load: "stats" },
    (j) => {
      try {
        var Jsn = JSON.parse(j);

        $('[data-stat-key="folder1"]').text(Jsn.stats.solved);
        $('[data-stat-key="folder2"]').text(Jsn.stats.waitingReply);
        $('[data-stat-key="folder3"]').text(Jsn.stats.waitingProcess);
        $('[data-stat-key="folder4"]').text(Jsn.stats.avgReply);

        if (Jsn.stats.unReaded.solved > 0) {
          $('[data-stat-key="folderPill1"]').text(Jsn.stats.unReaded.solved);
        } else {
          $('[data-stat-key="folderPill1"]').text("");
        }

        if (Jsn.stats.unReaded.waitingReply > 0) {
          $('[data-stat-key="folderPill2"]').text(
            Jsn.stats.unReaded.waitingReply
          );
        } else {
          $('[data-stat-key="folderPill2"]').text("");
        }

        if (Jsn.stats.unReaded.waitingProcess > 0) {
          $('[data-stat-key="folderPill3"]').text(
            Jsn.stats.unReaded.waitingProcess
          );
        } else {
          $('[data-stat-key="folderPill3"]').text("");
        }
      } catch (error) {
        AjaxFail();
      }
    }
  ).fail(() => {
    AjaxFail();
  });
};

var MarkReaded = () => {
  var SingleToken;
  var SelectedItems = $(
    ".email-application .user-action .vs-checkbox-con input:checked"
  )
    .closest("li")
    .map(function () {
      SingleToken = $(this).attr("data-token");
      $('[data-token="' + SingleToken + '"]').addClass("mail-read");
      return SingleToken;
    })
    .get();

  $("input:checked").prop("checked", false);

  if (SelectedItems.length > 0) {
    $.post(
      InternalAjaxHost + "web-service/tickets/mark/readed",
      { tickets: SelectedItems },
      (j) => {
        UpdateTicketStats();
      }
    ).fail(() => {
      AjaxFail();
    });
  }
};

var MarkUnReaded = () => {
  var SingleToken;
  var SelectedItems = $(
    ".email-application .user-action .vs-checkbox-con input:checked"
  )
    .closest("li")
    .map(function () {
      SingleToken = $(this).attr("data-token");
      $('[data-token="' + SingleToken + '"]').removeClass("mail-read");
      return SingleToken;
    })
    .get();

  $("input:checked").prop("checked", false);

  if (SelectedItems.length > 0) {
    $.post(
      InternalAjaxHost + "web-service/tickets/mark/unreaded",
      { tickets: SelectedItems },
      (j) => {
        UpdateTicketStats();
      }
    ).fail(() => {
      AjaxFail();
    });
  }
};

var MarkSolved = () => {
  var SingleToken;
  var SelectedItems = $(
    ".email-application .user-action .vs-checkbox-con input:checked"
  )
    .closest("li")
    .map(function () {
      SingleToken = $(this).attr("data-token");
      $('[data-token="' + SingleToken + '"] .bullet').attr(
        "class",
        "mr-1 bullet bullet-primary bullet-sm"
      );
      return SingleToken;
    })
    .get();

  $("input:checked").prop("checked", false);

  if (SelectedItems.length > 0) {
    $.post(
      InternalAjaxHost + "web-service/tickets/mark/solved",
      { tickets: SelectedItems },
      (j) => {
        UpdateTicketStats();
      }
    ).fail(() => {
      AjaxFail();
    });
  }
};

var TicketsRenderHtml = () => {
  $(".email-application .email-user-list .favorite i").on("click", function (
    e
  ) {
    var ClickedToken = $(this).parent(".favorite").attr("data-star-token");
    $(this).parent(".favorite").toggleClass("warning");
    e.stopPropagation();
    $.post(
      InternalAjaxHost + "web-service/ticket/favorite",
      { ticketToken: ClickedToken },
      (j) => {
        try {
          var JsnRes = JSON.parse(j);

          if (JsnRes.isStarred) {
            $('[data-star-token="' + JsnRes.ticketToken + '"]').addClass(
              "warning"
            );
          } else {
            $('[data-star-token="' + JsnRes.ticketToken + '"]').removeClass(
              "warning"
            );
          }
        } catch (error) {
          AjaxFail();
        }
      }
    ).fail(() => {
      AjaxFail();
    });
  });

  // On checkbox click stop propogation
  $(".email-user-list .vs-checkbox-con input").on("click", function (e) {
    e.stopPropagation();
  });
};

var ShowTicket = (ticketToken) => {
  $("#TicketDetailMessagesList").html("");
  $("#MailReplyFiles").html("");
  $("#reply_tc_mess").val("");
  $("#NewTicket").removeClass("show");
  $('[data-ticket-detail="subject"]').text("");
  $(".email-app-area").attr("style", "pointer-events:none;opacity:0.5");

  $.post(
    InternalAjaxHost + "web-service/get/ticket/detail",
    { ticket: ticketToken },
    (j) => {
      try {
        var Jsn = JSON.parse(j);
        $('[data-ticket-detail="subject"]').text(Jsn.ticketSubject);
        $('[data-ticket-detail="starred"]').removeClass("warning");
        $("#StarTicketDetailToken").attr(
          "data-ticket-detail-token",
          Jsn.ticketToken
        );
        $("#TicketDetailAddToken").val(Jsn.ticketToken);

        if (Jsn.isStarred) {
          $('[data-ticket-detail="starred"]').addClass("warning");
        }

        $('[data-ticket-detail="folder"]').attr(
          "class",
          "mr-1 bullet bullet-" + Jsn.ticketFolder.label + " bullet-sm"
        );
        $('[data-ticket-detail="foldertext"]').text(Jsn.ticketFolder.text);
        var OutHtml = "";
        $.each(Jsn.ticketMessages, (key, item) => {
          OutHtml +=
            '<div class="row">' +
            ' <div class="col-12">' +
            ' <div class="card px-1">' +
            ' <div class="card-header email-detail-head ml-75">' +
            '  <div  class="user-details d-flex justify-content-between align-items-center flex-wrap">' +
            '<div class="avatar mr-75">' +
            ' <img src="' +
            item.messageUser.avatar +
            '"  alt="avtar img holder" width="61" height="61">' +
            "</div>" +
            ' <div class="mail-items">' +
            ' <h4 class="list-group-item-heading mb-0">' +
            item.messageUser.realName +
            " </h4>" +
            '<div class="email-info-dropup dropdown">' +
            '<span class="font-small-3" aria-haspopup="true" aria-expanded="false">' +
            item.messageUser.role +
            "</span>" +
            " </div>" +
            " </div>" +
            " </div>" +
            ' <div class="mail-meta-item">' +
            '  <div class="mail-time mb-1">' +
            item.ticketTime.time +
            "</div>" +
            '  <div class="mail-date">' +
            item.ticketTime.date +
            "</div>" +
            " </div>" +
            " </div>" +
            ' <div class="card-body mail-message-wrapper pt-2 mb-0">' +
            '<div class="mail-message">' +
            item.ticketMessage +
            "</div>" +
            "</div>" +
            '<div class="mail-files py-2">';

          $.each(item.ticketFiles, (keyFile, itemFile) => {
            OutHtml +=
              '<a target="_blank" class="ml-2" href="/console/storage/download/' +
              itemFile.fileToken +
              '"><span class="chip-text"><div class="chip chip-primary">' +
              ' <div class="chip-body py-50">' +
              itemFile.fileName +
              "</span>" +
              "</div>" +
              " </div></a>";
          });

          OutHtml += " </div>" + " </div>" + " </div>" + "</div>";
        });

        $("#TicketDetailMessagesList").html(OutHtml);
        $('[data-token="' + Jsn.ticketToken + '"]').addClass("mail-read");
        $(".email-app-area").removeAttr("style");
        $("#TicketDetail").toggleClass("show");
      } catch (error) {
        AjaxFail();
      }
      UpdateTicketStats();
    }
  ).fail(() => {
    AjaxFail();
    UpdateTicketStats();
  });
};

var DeleteNewTicket = () => {
  $("#NewTicket").toggleClass("show");
  $("#MailFiles").html("");
  $("#helpInputTop").val("");
  $("#new_tc_mess").val("");
  $('[name="new_ticket_label"]').val(2);
};

var RefreshTickets = () => {
  var folderID = $(".list-group-item-action.active").attr("data-folder-id");
  GetTickets(folderID);
};

var UploadTicketFiles = () => {
  var $fileUpload = $("#InputTicketFiles");
  if (parseInt($fileUpload.get(0).files.length) > 3) {
    alert($("#MaxFileCountText").text());
  } else {
    FileUpload(
      "TicketFiles",
      () => {
        $("#TicketUploadFileLink").addClass("disabled_");
        $("#TicketUploadFileLoading").show();
      },
      (Jsn) => {
        var UploadedFiles = Jsn.uploadedFiles;
        $.each(UploadedFiles, (index, element) => {
          $("#MailFiles").append(
            '<a target="_blank" class="ml-2" href="/console/storage/download/' +
              element.token +
              '"><span class="chip-text"><div class="chip chip-primary">' +
              ' <div class="chip-body py-50">' +
              element.name +
              "</span>" +
              "</div>" +
              " </div></a>"
          );
        });
        $("#TicketUploadFileLink").removeClass("disabled_");
        $("#TicketUploadFileLoading").hide();
      }
    );
  }
};

var UploadReplyTicketFiles = () => {
  var $fileUpload = $("#InputTicketFilesReply");
  if (parseInt($fileUpload.get(0).files.length) > 3) {
    alert($("#MaxFileCountText").text());
  } else {
    FileUpload(
      "TicketFilesReply",
      () => {
        $("#TicketReplyUploadFileLink").addClass("disabled_");
        $("#TicketReplyUploadFileLoading").show();
      },
      (Jsn) => {
        var UploadedFiles = Jsn.uploadedFiles;
        $.each(UploadedFiles, (index, element) => {
          $("#MailReplyFiles").append(
            '<a target="_blank" class="ml-2" href="/console/storage/download/' +
              element.token +
              '"><span class="chip-text"><div class="chip chip-primary">' +
              ' <div class="chip-body py-50">' +
              element.name +
              "</span>" +
              "</div>" +
              " </div></a>"
          );
        });
        $("#TicketReplyUploadFileLink").removeClass("disabled_");
        $("#TicketReplyUploadFileLoading").hide();
      }
    );
  }
};

var StarTicketDetail = () => {
  var DetailToken = $("#StarTicketDetailToken").attr(
    "data-ticket-detail-token"
  );
  $.post(
    InternalAjaxHost + "web-service/ticket/favorite",
    { ticketToken: DetailToken },
    (j) => {
      try {
        var JsnRes = JSON.parse(j);

        if (JsnRes.isStarred) {
          $('[data-ticket-detail="starred"]').addClass("warning");
        } else {
          $('[data-ticket-detail="starred"]').removeClass("warning");
        }
      } catch (error) {
        AjaxFail();
      }
    }
  ).fail(() => {
    AjaxFail();
  });
};

var SendNewTicket = () => {
  if ($("#helpInputTop").val() != "" && $("#new_tc_mess").val() != "") {
    $("#NewTicketForm").addClass("disabled_");
    $("#CreateTcketButton").addClass("disabled_");
    var SelectForm = $("#NewTicketForm");
    $("input, textarea").trigger("blur");
    var FormData = $("#NewTicketForm").serialize();
    setTimeout(() => {
      $.ajax({
        url: InternalAjaxHost + "web-service/new/ticket",
        method: "POST",
        data: FormData,
        success: function (j) {
          try {
            var JsonFormData = JSON.parse(j);

            if (JsonFormData.status == "success") {
              toastr.success(JsonFormData.message, JsonFormData.title, {
                closeButton: true,
                timeOut: 8000,
              });
              RefreshTickets();
              setTimeout(() => {
                ShowTicket(JsonFormData.ticketToken);
              }, 500);
            } else {
              toastr.warning(JsonFormData.message, JsonFormData.title, {
                closeButton: true,
                timeOut: 8000,
              });
            }
          } catch (error) {
            AjaxFail();
          }
        },
        error: function () {
          AjaxFail();
        },
        complete: function () {
          SelectForm.removeClass("disabled_");
          $("#CreateTcketButton").removeClass("disabled_");
          UpdateTicketStats();
        },
      });
    }, 1000);
  }
};

var ReplyTicket = () => {
  if ($("#reply_tc_mess").val() != "") {
    $("#ReplyTicketForm").addClass("disabled_");
    $("#ReplyTcketButton").addClass("disabled_");
    var SelectForm = $("#ReplyTicketForm");
    $("input, textarea").trigger("blur");
    var FormData = $("#ReplyTicketForm").serialize();
    setTimeout(() => {
      $.ajax({
        url: InternalAjaxHost + "web-service/reply/ticket",
        method: "POST",
        data: FormData,
        success: function (j) {
          try {
            var JsonFormData = JSON.parse(j);

            if (JsonFormData.status == "success") {
              toastr.success(JsonFormData.message, JsonFormData.title, {
                closeButton: true,
                timeOut: 8000,
              });

              var SingleTicketMessageItem = "";

              SingleTicketMessageItem +=
                '<div class="row">' +
                ' <div class="col-12">' +
                ' <div class="card px-1">' +
                ' <div class="card-header email-detail-head ml-75">' +
                '  <div  class="user-details d-flex justify-content-between align-items-center flex-wrap">' +
                '<div class="avatar mr-75">' +
                ' <img src="' +
                JsonFormData.item.messageUser.avatar +
                '"  alt="avtar img holder" width="61" height="61">' +
                "</div>" +
                ' <div class="mail-items">' +
                ' <h4 class="list-group-item-heading mb-0">' +
                JsonFormData.item.messageUser.realName +
                " </h4>" +
                '<div class="email-info-dropup dropdown">' +
                '<span class="font-small-3" aria-haspopup="true" aria-expanded="false">' +
                JsonFormData.item.messageUser.role +
                "</span>" +
                " </div>" +
                " </div>" +
                " </div>" +
                ' <div class="mail-meta-item">' +
                '  <div class="mail-time mb-1">' +
                JsonFormData.item.ticketTime.time +
                "</div>" +
                '  <div class="mail-date">' +
                JsonFormData.item.ticketTime.date +
                "</div>" +
                " </div>" +
                " </div>" +
                ' <div class="card-body mail-message-wrapper pt-2 mb-0">' +
                '<div class="mail-message">' +
                JsonFormData.item.ticketMessage +
                "</div>" +
                "</div>" +
                '<div class="mail-files py-2">';

              $.each(JsonFormData.item.ticketFiles, (keyFile, itemFile) => {
                SingleTicketMessageItem +=
                  '<a target="_blank" class="ml-2" href="/console/storage/download/' +
                  itemFile.fileToken +
                  '"><span class="chip-text"><div class="chip chip-primary">' +
                  ' <div class="chip-body py-50">' +
                  itemFile.fileName +
                  "</span>" +
                  "</div>" +
                  " </div></a>";
              });

              SingleTicketMessageItem +=
                " </div>" + " </div>" + " </div>" + "</div>";

              $("#TicketDetailMessagesList").append(SingleTicketMessageItem);
              $("#reply_tc_mess").val("");
              $("#InputTicketFilesReply").val("");
              $("#MailReplyFiles").html("");
            } else {
              toastr.warning(JsonFormData.message, JsonFormData.title, {
                closeButton: true,
                timeOut: 8000,
              });
            }
          } catch (error) {
            AjaxFail();
          }
        },
        error: function () {
          AjaxFail();
        },
        complete: function () {
          SelectForm.removeClass("disabled_");
          $("#ReplyTcketButton").removeClass("disabled_");
        },
      });
    }, 1000);
  }
};

// Page loaded.
$("#UserRealNameNewTicket").text($("#UserRealName").text());
$("#NewTicketAvatar").attr("src", $(".usr_avatar_").attr("src"));
GetTickets(1);

setTimeout(() => {
  $("#TicketDetail").show();
  $("#NewTicket").show();
  $(".no-results").removeClass("d-none");
}, 1000);
