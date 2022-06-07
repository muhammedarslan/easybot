const RenderNormal = (data) => {
  return data;
};

const RenderBold = (data) => {
  return "<span style='font-weight:500;' >" + data + "</span>";
};

const RenderBar = (data) => {
  const barImp = data.split(",");
  return (
    '<div class="progress progress-bar-' +
    barImp[0] +
    '">' +
    '<div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100" style="width:' +
    barImp[1] +
    '%"></div>' +
    "</div>"
  );
};

const RenderStatus = (data) => {
  const StatusImp = data.split(",");
  return (
    '<div class="chip chip-' +
    StatusImp[0] +
    '">' +
    '<div class="chip-body">' +
    '<div class="chip-text">' +
    StatusImp[1] +
    "</div>" +
    " </div>" +
    " </div>"
  );
};

const RenderActionEditDelete = (data) => {
  const Imp = data.split(",");
  return (
    '<a href="' +
    Imp[0] +
    '"><span class="action-edit"><i class="feather icon-edit"></i></span></a>' +
    '<a style="margin-left:10px;" href="' +
    Imp[1] +
    '"><span class="action-delete"><i class="feather icon-trash"></i></span></a>'
  );
};

const RenderEdit = (data) => {
  return (
    '<a href="' +
    data +
    '"><span class="action-edit"><i class="feather icon-edit"></i></span></a>'
  );
};

const RenderDelete = (data) => {
  return (
    '<a href="' +
    data +
    '"><span class="action-delete"><i class="feather icon-trash"></i></span></a>'
  );
};

const ClickExport = (i) => {
  switch (i) {
    case 0:
      $(".buttons-print").click();
      break;
    case 1:
      $(".buttons-pdf").click();
      break;
    case 2:
      $(".buttons-excel").click();
      break;
    case 3:
      $(".buttons-csv").click();
      break;
    default:
      return false;
      break;
  }
};

const CreateTable = () => {
  const dataListView = $(".data-list-view").DataTable({
    responsive: true,
    ajax: {
      url: "",
      type: "GET",
      data: {
        load: "table",
      },
    },
    dom:
      '<"top"<"actions action-btns"B><"action-filters"lf>><"clear">rt<"bottom"<"actions">p>',
    aLengthMenu: [Options.PageMenu, Options.PageMenu],
    select: {
      style: "single",
    },
    order: [Options.Order],
    bInfo: false,
    pageLength: Options.PageLength,
    searching: Options.Search,
    buttons: [],
    oLanguage: {
      sUrl: "/assets/console/app-assets/datatable/" + AppLang + ".json",
    },
    columnDefs: ColumnDefsOptions,
    initComplete: function (settings, json) {
      $(".action-btns").prepend(
        '<h3 class="content-header-title float-left mb-0">' +
          TableTitle +
          "</h3>"
      );
      if (Options.Export == true) {
        $(".action-filters").prepend(
          '<div id="PrintDown" style="box-shadow:none;margin-right:7px;pointer-events:none;opacity:0.5;" class="btn-group dropdown actions-dropodown">' +
            ' <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle dropdown-toggle-msa waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="' +
            " width: 15.714rem;" +
            " text-align: center;" +
            " height: 3rem;" +
            " border-radius: 1.428rem;" +
            " border: 1px solid #DAE1E7;" +
            " font-size: 1rem;" +
            " background-position: calc(100% - 12px) 13px, calc(100% - 20px) 13px, 100% 0;" +
            ' ">' +
            SomeText.ExportText +
            "</button>" +
            ' <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 42px, 0px);">' +
            ' <a class="dropdown-item" onclick="ClickExport(0);" href="javascript:;"><i class="feather icon-printer"></i>' +
            SomeText.Print +
            "</a>" +
            ' <a class="dropdown-item" onclick="ClickExport(1);" href="javascript:;"><i class="feather icon-file-text"></i>' +
            SomeText.Pdf +
            "</a>" +
            ' <a class="dropdown-item" onclick="ClickExport(2);" href="javascript:;"><i class="feather icon-database"></i>' +
            SomeText.Excel +
            "</a>" +
            ' <a class="dropdown-item" onclick="ClickExport(3);" href="javascript:;"><i class="feather icon-menu"></i>' +
            SomeText.Csv +
            "</a>" +
            " </div>" +
            "  </div>"
        );

        setTimeout(() => {
          $("head").append(
            "<style>.dt-buttons{display:none !important;}</style>"
          );
          getScripts(
            [
              "/assets/console/app-assets/vendors/js/tables/datatable/buttons.html5.min.js",
              "/assets/console/app-assets/vendors/js/tables/datatable/buttons.print.min.js",
              "/assets/console/app-assets/datatable/pdfmake.min.js",
              "/assets/console/app-assets/datatable/vfs_fonts.js",
              "/assets/console/app-assets/datatable/jszip.min.js",
            ],
            () => {
              dataListView.button().add(0, {
                extend: "print",
                exportOptions: {
                  columns: ExportedColumns,
                },
              });
              dataListView.button().add(1, {
                extend: "pdf",
                exportOptions: {
                  columns: ExportedColumns,
                },
              });
              dataListView.button().add(2, {
                extend: "excel",
                exportOptions: {
                  columns: ExportedColumns,
                },
              });
              dataListView.button().add(3, {
                extend: "csv",
                exportOptions: {
                  columns: ExportedColumns,
                },
              });
              $("#PrintDown").attr(
                "style",
                "box-shadow:none;margin-right:7px;"
              );
            }
          );
        }, 100);
      }
    },
  });

  // Scrollbar
  if ($(".data-items").length > 0) {
    new PerfectScrollbar(".data-items", { wheelPropagation: false });
  }

  // mac chrome checkbox fix
  if (navigator.userAgent.indexOf("Mac OS X") != -1) {
    $(".dt-checkboxes-cell input, .dt-checkboxes").addClass("mac-checkbox");
  }
};
