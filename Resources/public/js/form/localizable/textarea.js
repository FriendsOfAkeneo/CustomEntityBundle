define([
    'custom_entity/form/localizable/text',
    'custom_entity/template/localizable/textarea'
], function (Text, template) {
    "use strict";

    return Text.extend({
        template: _.template(template)
    });
});