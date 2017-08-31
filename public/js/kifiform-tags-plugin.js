(function($) {
  "use strict";

  $("input.form-autocomplete")
    .once("finto-taxonomy-tag-insert")
    .on("kififormtaginsert", function(event, ui) {
      var tids = ui.autocomplete.data("finto_taxonomy-terms").split(/,/);

      if (ui.item.value.match(/\(p.*?\)$/)) {
        ui.tag.addClass("finto-taxonomy-tag");
        // console.log("FINTO TAG", ui.item.value);
      } else {
        var match = /\((\d+)\)$/.exec(ui.item.value);

        if (match && tids.indexOf(match[1]) != -1) {
          console.log("MOI", ui.item.value);
          ui.tag.addClass("finto-taxonomy-tag");
        }
      }
    });
}(jQuery));
