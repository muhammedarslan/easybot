var AddressChangeEvent = () => {
  var input = $("#label2");
  input.val(
    $("#label2")
      .val()
      .replace(/https?:\/\//gi, "")
  );
};

var getSearchParams = (url, k) => {
  var p = {};
  url.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (s, k, v) {
    p[decodeURIComponent(k)] = decodeURIComponent(v);
  });
  return k ? p[k] : p;
};

var addToQueryString = (href, key, value) => {
  var url = new URL($('select[name="876541_protocol"]').val() + "://" + href);
  url.searchParams.set(key, value);
  return url;
};

var removeToQueryString = (href, key, value) => {
  var url = new URL($('select[name="876541_protocol"]').val() + "://" + href);
  url.searchParams.delete(key);
  return url;
};

var isValidHttpUrl = (string) => {
  var res = string.match(
    /(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g
  );
  return res !== null;
};

var Tab1Area876541 = () => {
  var DataTab1Keys = $('input[name="876541_data1_key[]"]');
  var DataUrlAddress = getSearchParams($('input[name="876541_address"]').val());

  $.each(DataTab1Keys, (i, item) => {
    var singleKey = $(item).val();
    var singleVal = $('input[name="876541_data1_value[]"]').eq(i).val();

    if (singleKey == "" && singleVal == "") {
      if ($('input[name="876541_data1_key[]"]').length > 1) {
        $("#Tab1Table tr").eq(i).remove();
      } else {
        $('input[name="876541_data1_active[]').eq(i).prop("checked", false);
        $('input[name="876541_data1_key[]').eq(i).val("");
        $('input[name="876541_data1_value[]').eq(i).val("");
        $('textarea[name="876541_data1_description[]').eq(i).val("");
      }
    }

    if ($('input[name="876541_data1_key[]"]').length === i + 1) {
      UrlAddressSet();
    }
  });
};

var RemoveUnusedPrameters = () => {
  setTimeout(() => {
    var DataTab1Keys = $('input[name="876541_data1_key[]"]');
    var DataUrlAddress = getSearchParams(
      $('input[name="876541_address"]').val()
    );

    $.each(DataTab1Keys, (i, item) => {
      var singleKey = $(item).val();
      var singleVal = $('input[name="876541_data1_value[]"]').eq(i).val();

      if (singleKey == "" && singleVal == "") {
        if ($('input[name="876541_data1_key[]"]').length > 1) {
          $("#Tab1Table tr").eq(i).remove();
        } else {
          $('input[name="876541_data1_active[]').eq(i).prop("checked", false);
          $('input[name="876541_data1_key[]').eq(i).val("");
          $('input[name="876541_data1_value[]').eq(i).val("");
          $('textarea[name="876541_data1_description[]').eq(i).val("");
        }
      }

      if (DataUrlAddress[singleKey] == undefined && singleKey != "") {
        if ($('input[name="876541_data1_key[]"]').length > 1) {
          $("#Tab1Table tr").eq(i).remove();
        } else {
          $('input[name="876541_data1_active[]').eq(i).prop("checked", false);
          $('input[name="876541_data1_key[]').eq(i).val("");
          $('input[name="876541_data1_value[]').eq(i).val("");
          $('textarea[name="876541_data1_description[]').eq(i).val("");
        }
      }

      if ($('input[name="876541_data1_key[]"]').length === i + 1) {
        UrlAddressSet();
      }
    });
  }, 500);
};

var UrlAddressSet = () => {
  if ($('input[name="876541_address"]').val() != "") {
    var url = new URL(
      $('select[name="876541_protocol"]').val() +
        "://" +
        $('input[name="876541_address"]').val()
    );

    var protocol = $('select[name="876541_protocol"]').val();
    var address = $('input[name="876541_address"]')
      .val()
      .replace(/https?:\/\//gi, "");
    var url = new URL(protocol + "://" + address);
    var newAddress = url.href;
    $('input[name="876541_address"]').val(
      newAddress.replace(/https?:\/\//gi, "")
    );
  }
};

var ParameterChangeEventInput = () => {
  var UrlParams = [];
  var DataTab1Keys = $('input[name="876541_data1_key[]"]');
  var DataUrlAddress = getSearchParams($('input[name="876541_address"]').val());

  $.each(DataTab1Keys, (i, item) => {
    UrlParams[$(item).val()] = $('input[name="876541_data1_value[]"]')
      .eq(i)
      .val();

    UrlParams.push({
      key: $(item).val(),
      value: $('input[name="876541_data1_value[]"]').eq(i).val(),
      index: i,
      active:
        $('input[name="876541_data1_active[]"]').eq(i).prop("checked") == true
          ? true
          : false,
    });

    if (DataTab1Keys.length === i + 1) {
      $.each(DataUrlAddress, (key, value) => {
        var findItem = UrlParams.find((item) => item.key === key);

        if (findItem == undefined) {
          var appendTable = "<tr>" + $("#Tab1Table tr").eq(0).html() + "</tr>";
          $("#Tab1Table").append(appendTable);
          $('input[name="876541_data1_active[]').last().prop("checked", true);
          $('input[name="876541_data1_key[]').last().val(key);
          $('input[name="876541_data1_value[]').last().val(value);
          $('textarea[name="876541_data1_description[]').last().val("");
          CreateBotVariables();
        } else {
          if (findItem.value != value) {
            $('input[name="876541_data1_value[]').eq(findItem.index).val(value);
          }

          if (findItem.active == false) {
            $('input[name="876541_data1_active[]')
              .eq(findItem.index)
              .prop("checked", true);
          }
        }

        if (Object.keys(DataUrlAddress).pop() == key) {
          Tab1Area876541();
        }
      });
    }
  });
};

var ParameterChangeEvent = () => {
  var UrlParams = {};
  var DataTab1Keys = $('input[name="876541_data1_key[]"]');
  var DataUrlAddress = getSearchParams($('input[name="876541_address"]').val());

  $.each(DataTab1Keys, (i, item) => {
    if (
      $('input[name="876541_data1_active[]"]').eq(i).prop("checked") == true &&
      $(item).val() != ""
    ) {
      UrlParams[$(item).val()] = $('input[name="876541_data1_value[]"]')
        .eq(i)
        .val();
    }
    if (DataTab1Keys.length === i + 1) {
      $.each(UrlParams, (key, value) => {
        if (
          $('input[name="876541_address"]').val() != "" &&
          $('input[name="876541_address"]').val() != " "
        ) {
          if (DataUrlAddress[key] != value) {
            var newUrlAddress = addToQueryString(
              $('input[name="876541_address"]').val(),
              key,
              value
            ).href.replace(/https?:\/\//gi, "");
            $('input[name="876541_address"]').val(newUrlAddress);
          }
        }
      });

      $.each(DataUrlAddress, (key, value) => {
        if (UrlParams[key] == undefined) {
          var newUrlAddress = removeToQueryString(
            $('input[name="876541_address"]').val(),
            key,
            value
          ).href.replace(/https?:\/\//gi, "");
          $('input[name="876541_address"]').val(newUrlAddress);
        }
      });
    }
  });
};

var DeleteTab1Row = (e) => {
  var deleteIndex = $(e).index(".delete_link");
  var appendTable = "";
  if (deleteIndex == 0 && $(".delete_link").length == 1) {
    appendTable = "<tr>" + $("#Tab1Table tr").eq(0).html() + "</tr>";
  }
  $("#Tab1Table tr").eq(deleteIndex).remove();
  if (deleteIndex == 0 && $(".delete_link").length == 0) {
    setTimeout(() => {
      $("#Tab1Table").append(appendTable);
      $('input[name="876541_data1_active[]').last().prop("checked", true);
      $('input[name="876541_data1_key[]').last().val("");
      $('input[name="876541_data1_value[]').last().val("");
      $('textarea[name="876541_data1_description[]').last().val("");
      CreateBotVariables();
      ParameterChangeEvent();
    }, 500);
  }
};

var DeleteTab3Row = (e) => {
  var deleteIndex = $(e).index(".delete_data_3");
  var appendTable = "";
  if (deleteIndex == 0 && $(".delete_data_3").length == 1) {
    appendTable = "<tr>" + $("#Tab3Table tr").eq(0).html() + "</tr>";
  }
  $("#Tab3Table tr").eq(deleteIndex).remove();
  if (deleteIndex == 0 && $(".delete_data_3").length == 0) {
    setTimeout(() => {
      $("#Tab3Table").append(appendTable);
      $('input[name="876541_data3_active[]').last().prop("checked", true);
      $('input[name="876541_data3_key[]').last().val("");
      $('input[name="876541_data3_value[]').last().val("");
      $('textarea[name="876541_data3_description[]').last().val("");
      CreateBotVariables();
    }, 500);
  }
};

var DeleteTab4Row = (e) => {
  var deleteIndex = $(e).index(".delete_data_4");
  var appendTable = "";
  if (deleteIndex == 0 && $(".delete_data_4").length == 1) {
    appendTable = "<tr>" + $("#Tab4Table tr").eq(0).html() + "</tr>";
  }
  $("#Tab4Table tr").eq(deleteIndex).remove();
  if (deleteIndex == 0 && $(".delete_data_4").length == 0) {
    setTimeout(() => {
      $("#Tab4Table").append(appendTable);
      $('input[name="876541_data4_active[]').last().prop("checked", true);
      $('input[name="876541_data4_key[]').last().val("");
      $('input[name="876541_data4_value[]').last().val("");
      $('textarea[name="876541_data4_domain[]').last().val("");
      CreateBotVariables();
    }, 500);
  }
};

var ValueChangeTab1 = () => {
  var AddNew = true;
  $.each($('input[name="876541_data1_key[]"]'), (i, item) => {
    if ($(item).val() == "") {
      AddNew = false;
    }
    if ($('input[name="876541_data1_key[]').length === i + 1) {
      if (AddNew == true) {
        $("#Tab1Table").append(
          "<tr>" + $("#Tab1Table tr").eq(0).html() + "</tr>"
        );
        $('input[name="876541_data1_active[]').last().prop("checked", false);
        $('input[name="876541_data1_key[]').last().val("");
        $('input[name="876541_data1_value[]').last().val("");
        $('textarea[name="876541_data1_description[]').last().val("");
        CreateBotVariables();
      }
    }
  });
};

var ValueChangeTab3 = () => {
  var AddNew = true;
  $.each($('input[name="876541_data3_key[]"]'), (i, item) => {
    if ($(item).val() == "") {
      AddNew = false;
    }
    if ($('input[name="876541_data3_key[]').length === i + 1) {
      if (AddNew == true) {
        $("#Tab3Table").append(
          "<tr>" + $("#Tab3Table tr").eq(0).html() + "</tr>"
        );
        $('input[name="876541_data3_active[]').last().prop("checked", false);
        $('input[name="876541_data3_key[]').last().val("");
        $('input[name="876541_data3_value[]').last().val("");
        $('textarea[name="876541_data3_description[]').last().val("");
        CreateBotVariables();
      }
    }
  });
};

var ValueChangeTab4 = () => {
  var AddNew = true;
  $.each($('input[name="876541_data4_key[]"]'), (i, item) => {
    if ($(item).val() == "") {
      AddNew = false;
    }
    if ($('input[name="876541_data4_key[]').length === i + 1) {
      if (AddNew == true) {
        $("#Tab4Table").append(
          "<tr>" + $("#Tab4Table tr").eq(0).html() + "</tr>"
        );
        $('input[name="876541_data4_active[]').last().prop("checked", false);
        $('input[name="876541_data4_key[]').last().val("");
        $('input[name="876541_data4_value[]').last().val("");
        $('textarea[name="876541_data4_domain[]').last().val("");
        CreateBotVariables();
      }
    }
  });
};

var Tab3RadioClick = () => {
  var selectedRadio = $("input[name='876541_data3_radio']:checked").val();
  $(".easycreate_tab3").hide();
  switch (selectedRadio) {
    case "0":
      $(".easycreate_tab3_0").fadeIn();
      break;
    case "1":
      $(".easycreate_tab3_1").fadeIn();
      break;
    case "2":
      $(".easycreate_tab3_1").fadeIn();
      break;
    case "3":
      $(".easycreate_tab3_3").fadeIn();
      break;

    default:
      break;
  }
};

var DeleteTab2Row = (e) => {
  var deleteIndex = $(e).index(".delete_data_2");
  var appendTable = "";
  if (deleteIndex == 3 && $(".delete_data_2").length == 4) {
    appendTable = "<tr>" + $("#Tab2Table tr").eq(3).html() + "</tr>";
  }
  $("#Tab2Table tr").eq(deleteIndex).remove();
  if (deleteIndex == 3 && $(".delete_data_2").length == 3) {
    setTimeout(() => {
      $("#Tab2Table").append(appendTable);
      $('input[name="876541_data2_active[]').last().prop("checked", true);
      $('input[name="876541_data2_key[]').last().val("");
      $('input[name="876541_data2_value[]').last().val("");
      $('textarea[name="876541_data2_description[]').last().val("");
      CreateBotVariables();
    }, 500);
  }
};

var ValueChangeTab2 = () => {
  var AddNew = true;
  $.each($('input[name="876541_data2_key[]"]'), (i, item) => {
    if ($(item).val() == "") {
      AddNew = false;
    }
    if ($('input[name="876541_data2_key[]').length === i + 1) {
      if (AddNew == true) {
        $("#Tab2Table").append(
          "<tr>" + $("#Tab2Table tr").eq(3).html() + "</tr>"
        );
        $('input[name="876541_data2_active[]').last().prop("checked", false);
        $('input[name="876541_data2_key[]').last().val("");
        $('input[name="876541_data2_value[]').last().val("");
        $('textarea[name="876541_data2_description[]').last().val("");
        CreateBotVariables();
      }
    }
  });
};

var iconFormat = (icon) => {
  var originalOption = icon.element;
  if (!icon.id) {
    return icon.text;
  }
  var $icon =
    "<i class='" + $(icon.element).data("icon") + "'></i>" + icon.text;

  return $icon;
};

var proxyFormat = (icon) => {
  var originalOption = icon.element;
  if (!icon.id) {
    return icon.text;
  }
  var $icon =
    "<i class='" +
    $(icon.element).data("icon") +
    "'></i>" +
    icon.text +
    '<span id="ProxyIpCount"></span>';

  return $icon;
};

var ProxyCountryChange = () => {
  var country = $('select[name="876541_proxy_country"]').val();
  $.post(
    InternalAjaxHost + "web-service/proxy/country/change",
    { c: country },
    (j) => {
      try {
        var Jsn = JSON.parse(j);

        if (Jsn.showCount == true) {
          $("#ProxyIpCount").text(
            "(" + Jsn.countryCode + ": " + Jsn.proxyCount + " ip" + ")"
          );
        } else {
          $("#ProxyIpCount").text("");
        }
      } catch (error) {
        AjaxFail();
      }
    }
  ).fail(() => {
    AjaxFail();
  });
};

var Back2Create = () => {
  Swal.fire({
    title: $('#Page876541Texts [data-key="BackSwalTitle"]').text(),
    text: $('#Page876541Texts [data-key="BackSwalMessage"]').text(),
    type: "info",
    showCancelButton: true,
    onOpen: () => {
      $(".swal2-cancel").focus();
    },
    confirmButtonText: $(
      '#Page876541Texts [data-key="BackSwalButton1"]'
    ).text(),
    cancelButtonText: $('#Page876541Texts [data-key="BackSwalButton2"]').text(),
  }).then((result) => {
    if (result.value) {
      var Path = window.location.pathname;
      var split = Path.split("/");
      var urlToken = split[4];
      Swal.close();
      barba.go("/console/create/bot/" + urlToken);
    }
  });
};

var matchStart = (params, data) => {
  if ($.trim(params.term) === "") {
    return data;
  }

  if (typeof data.children === "undefined") {
    return null;
  }

  var filteredChildren = [];
  $.each(data.children, function (idx, child) {
    if (child.text.toUpperCase().indexOf(params.term.toUpperCase()) == 0) {
      filteredChildren.push(child);
    }
  });

  if (filteredChildren.length) {
    var modifiedData = $.extend({}, data, true);
    modifiedData.children = filteredChildren;

    return modifiedData;
  }

  return null;
};

var CreateBotVariables = () => {
  var Path = window.location.pathname;
  var split = Path.split("/");
  var urlToken = split[4];
  $.post(
    InternalAjaxHost + "web-service/create/bot/variables",
    { token: urlToken },
    (j) => {
      try {
        var Jsn = JSON.parse(j);
        $('[data-show-variables="on"]').popover({
          placement: "bottom",
          container: "main",
          title: Jsn.title,
          trigger: "focus",
          delay: {
            show: 300,
            hide: 0,
          },
          html: true,
          content: function () {
            return "MSA";
          },
        });
      } catch (error) {
        AjaxFail();
      }
    }
  ).fail(() => {
    AjaxFail();
  });
};

var SendSandboxRequest = (
  QuerypageToken,
  QueryrequestToken,
  QueryStartBot = "false"
) => {
  $.post(
    InternalAjaxHost + "web-service/sandbox/static/data",
    {
      pageToken: QuerypageToken,
      requestToken: QueryrequestToken,
      startBot: QueryStartBot,
    },
    (jsn) => {
      try {
        var d = JSON.parse(jsn);

        if (d.status == "success") {
          if (d.process == "wait") {
            $(".sandboxWaiting").text(d.waitMessage);
            setTimeout(() => {
              SendSandboxRequest(d.tokens.pageToken, d.tokens.requestToken);
            }, 10000);
          } else if (d.process == "processing") {
            setTimeout(() => {
              $(".sandboxWaiting").text(d.processMessage);
            }, 1000);
            SendSandboxRequest(
              d.tokens.pageToken,
              d.tokens.requestToken,
              "true"
            );
          } else {
            $(".sandboxWaiting").text(d.processMessage);
            if (d.location == true) {
              setTimeout(() => {
                swal.close();
                barba.go(
                  "/console/parse/html/" +
                    d.tokens.pageToken +
                    "/876541/" +
                    d.tokens.requestToken +
                    "/t/" +
                    d.unixTimeStap
                );
              }, 500);
            }
          }
        } else {
          Swal.fire({
            title: d.title,
            text: d.message,
            type: "warning",
            showConfirmButton: false,
            showCancelButton: true,
            onOpen: () => {
              $(".swal2-cancel").blur();
            },
            cancelButtonText: d.buttons.cancel,
          });
        }
      } catch (error) {
        swal.close();
        AjaxFail();
        $.post(
          InternalAjaxHost + "web-service/sandbox/request/finish",
          {
            pageToken: QuerypageToken,
            requestToken: QueryrequestToken,
          },
          () => {}
        );
      }
    }
  ).fail(() => {
    swal.close();
    AjaxFail();
    $.post(
      InternalAjaxHost + "web-service/sandbox/request/finish",
      {
        pageToken: QuerypageToken,
        requestToken: QueryrequestToken,
      },
      () => {}
    );
  });
};

// Page loaded.
CreateBotVariables();

$(".select2-icons").select2({
  dropdownAutoWidth: true,
  width: "100%",
  matcher: matchStart,
  templateResult: iconFormat,
  templateSelection: iconFormat,
  escapeMarkup: function (es) {
    return es;
  },
});

$(".select2-icons-proxy").select2({
  dropdownAutoWidth: true,
  width: "100%",
  matcher: matchStart,
  templateResult: proxyFormat,
  templateSelection: proxyFormat,
  escapeMarkup: function (es) {
    return es;
  },
});

setTimeout(() => {
  ProxyCountryChange();
}, 1000);

SubmitForm("CreateBot876541Form", "create/bot/876541", (j) => {
  if (j.status == "success") {
    Swal.fire({
      title: j.translations.swal.title,
      html: j.translations.swal.text,
      type: "success",
      showCancelButton: true,
      confirmButtonText: j.translations.swal.confirmButtonText,
      cancelButtonText: j.translations.swal.cancelButtonText,
    }).then((result) => {
      if (result.value) {
        Swal.fire({
          title: j.translations.swal.processing,
          imageUrl: "/assets/media/loading_server.gif",
          html: '<span class="sandboxWaiting" ></span>',
          allowEscapeKey: false,
          allowOutsideClick: false,
          confirmButtonClass: "btn btn-primary",
          showConfirmButton: false,
          showCancelButton: false,
          buttonsStyling: false,
        });

        setTimeout(() => {
          SendSandboxRequest(j.pageToken, j.requestToken);
        }, 1500);
      }
    });
  }
});
