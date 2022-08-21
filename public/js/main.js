if (document.getElementById("body")) {
  ClassicEditor.create(document.querySelector("#body"), {
    toolbar: [
      "bold",
      "italic",
      "link",
      "bulletedList",
      "numberedList",
      "blockQuote",
    ],
  })
    .then((editor) => {
      console.log("Editor Initialized");
    })
    .catch((error) => {
      console.error(error);
    });
}

$(document).ready(function () {
  $("#dataTable").DataTable({
    lengthChange: true,
    pageLength: 100,
    lengthMenu: [100, 250, 500, 750, 1000, "All"],
  });
});
