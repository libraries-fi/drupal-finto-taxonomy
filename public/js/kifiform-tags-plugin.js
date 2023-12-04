(function($) {
  "use strict";

  $(once("finto-taxonomy-tag-insert", "input.form-autocomplete"))
    .on("kififormtaginsert", function(event, ui) {
      var tids = ui.autocomplete[0].dataset.finto_taxonomyTerms.split(/,/);

      if (ui.item.value.match(/\(p.*?\)$/)) {
        ui.tag.addClass("finto-taxonomy-tag");
      } else {
        var match = /\((\d+)\)$/.exec(ui.item.value);

        if (match && tids.indexOf(match[1]) != -1) {
          ui.tag.addClass("finto-taxonomy-tag");
        }
      }
    });
}(jQuery));
