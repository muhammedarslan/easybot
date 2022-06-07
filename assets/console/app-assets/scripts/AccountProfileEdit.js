var LoadProfileInfo = () => {
  $("form").removeAttr("style");
  $.post(
    InternalAjaxHost + "web-service/my/profile",
    { load: "profile" },
    (j) => {
      try {
        var JsnD = JSON.parse(j);

        setTimeout(() => {
          $("#AvatarSrc").attr("src", JsnD.avatar);
        }, 500);

        $.each(JsnD.profile, (index, element) => {
          $('[data-key="' + index + '"]').html(element);
        });

        $.each(JsnD.profile, (index, element) => {
          $('[data-val="' + index + '"]').val(element);
        });

        if (JsnD.isDefaultAvatar == true) {
          $(".rmv_avatar").attr("style", "pointer-events:none;opacity:0.5");
        }

        $("#UserRealName").text(JsnD.profile.fullname);
      } catch (error) {
        AjaxFail();
      }
    }
  ).fail(() => {
    AjaxFail();
  });
};

var ChangeAvatar = () => {
  $("#profile_avatar").click();
};

var UploadAvatar = () => {
  $(".rmv_avatar").attr("style", "pointer-events:none;opacity:0.5");
  $(".edt_avatar").attr("style", "pointer-events:none;opacity:0.5");
  $("#AvatarSrc").attr("src", "/assets/media/img_loading.gif");
  var oldAvatarSrc = $(".usr_avatar_").attr("src");
  $(".usr_avatar_").attr("src", "/assets/media/img_loading.gif");
  topbar.show();

  $.ajax({
    url: InternalAjaxHost + "web-service/upload/avatar",
    type: "POST",
    data: new FormData($(".UploadAvatarForm").last()[0]),
    contentType: false,
    cache: false,
    processData: false,
    success: function (data) {
      try {
        var UploadJsn = JSON.parse(data);

        if (UploadJsn.status == "success") {
          toastr.success(UploadJsn.message, UploadJsn.title, {
            closeButton: true,
            timeOut: 8000,
          });
          $(".usr_avatar_").attr("src", UploadJsn.avatarUrl);
          $(".rmv_avatar").removeAttr("style");
          $(".edt_avatar").removeAttr("style");
          LoadProfileInfo();
        } else {
          toastr.warning(UploadJsn.message, UploadJsn.title, {
            closeButton: true,
            timeOut: 8000,
          });
          $(".usr_avatar_").attr("src", oldAvatarSrc);
          $(".rmv_avatar").removeAttr("style");
          $(".edt_avatar").removeAttr("style");
          LoadProfileInfo();
        }
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

var RemoveAvatar = () => {
  Swal.fire({
    title: $('#EditProfileTexts [data-key="DeleteAvatarModalTitle"]').text(),
    text: $('#EditProfileTexts [data-key="DeleteAvatarModalMessage"]').text(),
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: $(
      '#EditProfileTexts [data-key="DeleteAvatarModalButton1"]'
    ).text(),
    cancelButtonText: $(
      '#EditProfileTexts [data-key="DeleteAvatarModalButton2"]'
    ).text(),
  }).then((result) => {
    if (result.value) {
      $(".rmv_avatar").attr("style", "pointer-events:none;opacity:0.5");
      $(".edt_avatar").attr("style", "pointer-events:none;opacity:0.5");
      $("#AvatarSrc").attr("src", "/assets/media/img_loading.gif");
      $(".usr_avatar_").attr("src", "/assets/media/img_loading.gif");

      setTimeout(() => {
        $.post(
          InternalAjaxHost + "web-service/remove/avatar",
          { remove: "avatar" },
          (avatar) => {
            $(".usr_avatar_").attr("src", avatar);
            $(".rmv_avatar").removeAttr("style");
            $(".edt_avatar").removeAttr("style");
            LoadProfileInfo();
          }
        ).fail(() => {
          AjaxFail();
        });
      }, 1000);
    }
  });
};

var PhoneNumberChanged = () => {
  LoadProfileInfo();
  CheckNotification();
  toastr.success(
    $('#EditProfileTexts [data-key="PhoneChangedMessage"]').text(),
    $('#EditProfileTexts [data-key="PhoneChangedTitle"]').text(),
    {
      closeButton: true,
      timeOut: 8000,
    }
  );
};

var PhoneNumberChangeStep2 = () => {
  $.post(
    InternalAjaxHost + "web-service/phone/change/pin",
    { get: "info" },
    (pinInfo) => {
      try {
        var js = JSON.parse(pinInfo);
        PinVerify(js);
      } catch (error) {
        AjaxFail();
      }
    }
  ).fail(() => {
    AjaxFail();
  });
};

LoadProfileInfo();
$('[data-toggle="popover"]').popover();
$("#PhnNumbersEdit").inputmask("+99 999 999 99 99");

SubmitForm("EditMyProfileForm", "edit/profile", (Jsn) => {
  if (Jsn.ChangeAppMode) {
    ChangeThemeMode();
  }

  if (Jsn.ChangeLanguage) {
    setTimeout(() => {
      window.location = "?hl=" + Jsn.ChangedLang;
    }, 500);
  }

  if (Jsn.openAccountValidateModal) {
    AccountValidate();
    SubmitValidateForm(Jsn.accountValidatePhoneNumber);
  }

  if (Jsn.pinVerifyModal) {
    PinVerify(Jsn.pinVerifyInfo);
  }

  if (Jsn.passwordShowToast) {
    toastr[Jsn.passwordToastType](
      Jsn.passwordToast.message,
      Jsn.passwordToast.title,
      {
        closeButton: true,
        timeOut: 8000,
      }
    );
    $(".cng_password").val("");
  }

  if (Jsn.phoneErrorToast) {
    toastr.warning(Jsn.phoneErrorToastMessage, Jsn.phoneErrorToastTitle, {
      closeButton: true,
      timeOut: 8000,
    });
  }

  LoadProfileInfo();
});
