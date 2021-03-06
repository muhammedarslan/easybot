"use strict";

var GetInboxJ = () => {
  $.post(
    InternalAjaxHost + "web-service/get/inbox",
    { load: "inbox" },
    (data) => {
      var JsnData = JSON.parse(data);

      if (JsnData.status == "success") {
        var HtmlOutput = "";
        var SingleItem;
        $.each(JsnData.items, (item) => {
          SingleItem = JsnData.items[item];

          HtmlOutput +=
            '<li data-token="' +
            SingleItem.itemToken +
            '" class="media">' +
            ' <div class="media-body">' +
            ' <div class="user-details">' +
            ' <div class="mail-items">' +
            '  <h5 class="list-group-item-heading text-bold-600 mb-25">' +
            "   Easybot </h5>" +
            ' <span class="list-group-item-text text-truncate">' +
            SingleItem.Email.Subject +
            "</span>" +
            " </div>" +
            ' <div class="mail-meta-item">' +
            ' <span class="float-right">' +
            "  <span" +
            '    class="mr-1 bullet bullet-success bullet-sm"></span>' +
            ' <span class="mail-date">' +
            SingleItem.Email.Time +
            "</span>" +
            " </span>" +
            " </div>" +
            " </div>" +
            ' <div class="mail-message">' +
            ' <p class="list-group-item-text mb-0 truncate">' +
            SingleItem.Email.Text +
            "</p>" +
            " </div>" +
            "</div>" +
            "</li>";
        });

        $("#ListMails").html(HtmlOutput);

        if (HtmlOutput == "") {
          $(".email-user-list .no-results").addClass("show");
        }

        setTimeout(() => {
          // if it is not touch device
          if (!$.app.menu.is_touch_device()) {
            // Email left Sidebar
            if ($(".sidebar-menu-list").length > 0) {
              var sidebar_menu_list = new PerfectScrollbar(
                ".sidebar-menu-list"
              );
            }

            // User list scroll
            if ($(".email-user-list").length > 0) {
              var users_list = new PerfectScrollbar(".email-user-list");
            }

            // Email detail section
            if ($(".email-scroll-area").length > 0) {
              var users_list = new PerfectScrollbar(".email-scroll-area");
            }

            // Modal dialog scroll
            if ($(".modal-dialog-scrollable .modal-body").length > 0) {
              var sidebar_menu_list = new PerfectScrollbar(
                ".modal-dialog-scrollable .modal-body"
              );
            }
          }

          // if it is a touch device
          else {
            $(".sidebar-menu-list").css("overflow", "scroll");
            $(".email-user-list").css("overflow", "scroll");
            $(".email-scroll-area").css("overflow", "scroll");
            $(".modal-dialog-scrollable .modal-body").css("overflow", "scroll");
          }

          // Main menu toggle should hide app menu
          $(".menu-toggle").on("click", function (e) {
            $(".app-content .sidebar-left").removeClass("show");
            $(".app-content .app-content-overlay").removeClass("show");
          });

          // On sidebar close click
          $(".email-application .sidebar-close-icon").on("click", function () {
            $(".sidebar-left").removeClass("show");
            $(".app-content-overlay").removeClass("show");
          });

          // Email sidebar toggle
          $(".sidebar-toggle").on("click", function (e) {
            e.stopPropagation();
            $(".app-content .sidebar-left").toggleClass("show");
            $(".app-content .app-content-overlay").addClass("show");
          });
          $(".app-content .app-content-overlay").on("click", function (e) {
            $(".app-content .sidebar-left").removeClass("show");
            $(".app-content .app-content-overlay").removeClass("show");
          });

          // Email Right sidebar toggle
          $(".email-app-list .email-user-list li").on("click", function (e) {
            $(".MainContent").attr("style", "pointer-events:none;opacity:0.3");
            $("#Mail_Height").attr("style", "height:1500px;");
            setTimeout(() => {
              $.post(
                "/web-service/get/email",
                { token: $(this).attr("data-token") },
                (data) => {
                  try {
                    let js = JSON.parse(data);
                    if (js.process == "success") {
                      $("#Mail_Title").text(js.title);
                      $("#Mail_Time1").text(js.time1);
                      $("#Mail_Time2").text(js.time2);
                      $("#Mail_Iframe").html(
                        '<iframe onload="this.height=this.contentWindow.document.body.scrollHeight + 130;" src="/mail/' +
                          js.token +
                          '" frameborder="none" scrolling="none" style="width: 100%"></iframe>'
                      );
                      $(".app-content .email-app-details").toggleClass("show");
                    }
                    $(".MainContent").removeAttr("style");
                  } catch (error) {
                    AjaxFail();
                  }
                }
              ).fail(() => {
                AjaxFail();
              });
            }, 200);
          });

          // Add class active on click of sidebar list
          $(".email-application .list-group-messages a").on(
            "click",
            function () {
              if (
                $(".email-application .list-group-messages a").hasClass(
                  "active"
                )
              ) {
                $(".email-application .list-group-messages a").removeClass(
                  "active"
                );
              }
              $(this).addClass("active");
            }
          );

          // Email detail view back button click
          $(".go-back").on("click", function (e) {
            e.stopPropagation();
            $("#Mail_Height").removeAttr("style");
            $(".app-content .email-app-details").removeClass("show");
          });

          // For app sidebar on small screen
          if ($(window).width() > 768) {
            if ($(".app-content .app-content-overlay").hasClass("show")) {
              $(".app-content .app-content-overlay").removeClass("show");
            }
          }
          // Favorite star click
          $(".email-application .favorite i").on("click", function (e) {
            $(this).parent(".favorite").toggleClass("warning");
            e.stopPropagation();
          });

          // On checkbox click stop propogation
          $(".email-user-list .vs-checkbox-con input").on("click", function (
            e
          ) {
            e.stopPropagation();
          });

          // Select all checkbox
          $(document).on(
            "click",
            ".email-app-list .selectAll input",
            function () {
              $(".user-action .vs-checkbox-con input").prop(
                "checked",
                this.checked
              );
            }
          );

          // Delete Mail from list
          $(".email-application .mail-delete").on("click", function () {
            $(".email-application .user-action .vs-checkbox-con input:checked")
              .closest("li")
              .remove();
            $(".email-application .selectAll input").prop("checked", "");
          });

          // Mark mail unread
          $(".email-application .mail-unread").on("click", function () {
            $(".email-application .user-action .vs-checkbox-con input:checked")
              .closest("li")
              .removeClass("mail-read");
          });

          // Filter
          $(".email-app-list #email-search").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            if (value != "") {
              $(".email-user-list .users-list-wrapper li").filter(function () {
                $(this).toggle(
                  $(this).text().toLowerCase().indexOf(value) > -1
                );
              });
              var tbl_row = $(".email-user-list .users-list-wrapper li:visible")
                .length; //here tbl_test is table name

              //Check if table has row or not
              if (tbl_row == 0) {
                $(".email-user-list .no-results").addClass("show");
              } else {
                if ($(".email-user-list .no-results").hasClass("show")) {
                  $(".email-user-list .no-results").removeClass("show");
                }
              }
            } else {
              // If filter box is empty
              $(".email-user-list .users-list-wrapper li").show();
              if ($(".email-user-list .no-results").hasClass("show")) {
                $(".email-user-list .no-results").removeClass("show");
              }
            }
          });

          $(window).on("resize", function () {
            // remove show classes from sidebar and overlay if size is > 992
            if ($(window).width() > 768) {
              if ($(".app-content .app-content-overlay").hasClass("show")) {
                $(".app-content .sidebar-left").removeClass("show");
                $(".app-content .app-content-overlay").removeClass("show");
              }
            }
          });
          setTimeout(() => {
            $(".email-app-details").show();
          }, 500);
        }, 500);
      } else {
        getScripts(
          ["/assets/console/app-assets/js/core/validate.js"],
          () => {}
        );
      }
    }
  ).fail(() => {
    AjaxFail();
  });
};

setTimeout(() => {
  GetInboxJ();
}, 500);
