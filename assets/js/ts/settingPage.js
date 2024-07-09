System.register(["@wordpress/data"], function (exports_1, context_1) {
    "use strict";
    var data_1;
    var __moduleName = context_1 && context_1.id;
    return {
        setters: [
            function (data_1_1) {
                data_1 = data_1_1;
            }
        ],
        execute: function () {
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelector('.add-row').addEventListener('click', function () {
                    var table = document.getElementById('dynamic-form');
                    var rowCount = table.rows.length - 1;
                    var newRow = table.insertRow(-1);
                    newRow.innerHTML = "\n          <td><input type=\"text\" name=\"my_custom_settings[".concat(rowCount, "][id]\"></td>\n          <td><input type=\"text\" name=\"my_custom_settings[").concat(rowCount, "][url]\"></td>\n          <td><button type=\"button\" class=\"button remove-row\">Remove</button></td>\n      ");
                });
                document.getElementById('dynamic-form').addEventListener('click', function (event) {
                    if (event.target && event.target.matches('button.remove-row')) {
                        event.target.closest('tr').remove();
                    }
                });
            });
            document.addEventListener('DOMContentLoaded', function () {
                var initialTemplate = data_1.select('core/editor').getEditedPostAttribute('template');
                data_1.subscribe(function () {
                    var isSavingPost = data_1.select('core/editor').isSavingPost();
                    var isAutosavingPost = data_1.select('core/editor').isAutosavingPost();
                    var currentTemplate = data_1.select('core/editor').getEditedPostAttribute('template');
                    console.log(initialTemplate, currentTemplate);
                    if (initialTemplate !== currentTemplate) {
                        initialTemplate = currentTemplate;
                        if (isSavingPost && !isAutosavingPost) {
                            location.reload();
                        }
                    }
                });
            });
        }
    };
});
//# sourceMappingURL=settingPage.js.map