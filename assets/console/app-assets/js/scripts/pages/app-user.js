/*=========================================================================================
    File Name: app-user.js
    Description: User page
    --------------------------------------------------------------------------------------
    Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/
$(document).ready(function () {
  var isRtl;
  if ($("html").attr("data-textdirection") == "rtl") {
    isRtl = true;
  } else {
    isRtl = false;
  }

  //  Rendering badge in status column
  var customBadgeHTML = function (params) {
    var color = "";
    if (params.value == "active") {
      color = "success";
      return (
        "<div class='badge badge-pill badge-light-" +
        color +
        "' >" +
        params.value +
        "</div>"
      );
    } else if (params.value == "blocked") {
      color = "danger";
      return (
        "<div class='badge badge-pill badge-light-" +
        color +
        "' >" +
        params.value +
        "</div>"
      );
    } else if (params.value == "deactivated") {
      color = "warning";
      return (
        "<div class='badge badge-pill badge-light-" +
        color +
        "' >" +
        params.value +
        "</div>"
      );
    }
  };

  //  Rendering bullet in verified column
  var customBulletHTML = function (params) {
    var color = "";
    if (params.value == true) {
      color = "success";
      return "<div class='bullet bullet-sm bullet-" + color + "' >" + "</div>";
    } else if (params.value == false) {
      color = "secondary";
      return "<div class='bullet bullet-sm bullet-" + color + "' >" + "</div>";
    }
  };

  // Renering Icons in Actions column
  var customIconsHTML = function (params) {
    var usersIcons = document.createElement("span");
    var editIconHTML =
      "<a href='app-user-edit.html'><i class= 'users-edit-icon feather icon-edit-1 mr-50'></i></a>";
    var deleteIconHTML = document.createElement("i");
    var attr = document.createAttribute("class");
    attr.value = "users-delete-icon feather icon-trash-2";
    deleteIconHTML.setAttributeNode(attr);
    // selected row delete functionality
    deleteIconHTML.addEventListener("click", function () {
      deleteArr = [params.data];
      // var selectedData = gridOptions.api.getSelectedRows();
      gridOptions.api.updateRowData({
        remove: deleteArr,
      });
    });
    usersIcons.appendChild($.parseHTML(editIconHTML)[0]);
    usersIcons.appendChild(deleteIconHTML);
    return usersIcons;
  };

  //  Rendering avatar in username column
  var customAvatarHTML = function (params) {
    return (
      "<span class='avatar'><img src='" +
      params.data.avatar +
      "' height='32' width='32'></span>" +
      params.value
    );
  };
});
