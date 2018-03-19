'use strict';

define([
        'jquery',
        'underscore',
        'pim/form/common/fields/field',
        'pim/media-url-generator',
        'routing',
        'custom_entity/template/form/common/fields/media'
    ],
    function ($,
              _,
              BaseField,
              MediaUrlGenerator,
              Routing,
              template) {
        return BaseField.extend({
            template: _.template(template),

            events: {
                'change input[type="file"]': function (event) {
                    this.errors = [];
                    this.updateModel(this.getFieldValue(event.target));
                    this.render();
                },
                'click .clear-field': 'clearField'
            },

            /**
             * {@inheritdoc}
             */
            renderInput: function (templateContext) {
                var modelValue = this.getModelValue();
                var originalFilename = null;
                var filePath = null;
                var shortFilePath = null;
                if (modelValue != null) {
                    var modelValueAsJson = JSON.parse(modelValue);
                    originalFilename = modelValueAsJson.originalFilename;
                    filePath = modelValueAsJson.filePath;
                    shortFilePath = modelValueAsJson.shortFilePath;
                }

                return this.template(_.extend(templateContext, {
                    value: modelValue,
                    originalFilename: originalFilename,
                    filePath: filePath,
                    shortFilePath: shortFilePath,
                    mediaUrlGenerator: MediaUrlGenerator
                }));
            },

            /**
             * {@inheritdoc}
             */
            getFieldValue: function (field) {
                var self = this;
                var fieldValue = null;

                var input = this.$(field).get(0);
                if (!input || 0 === input.files.length) {
                    return null;
                }
                var formData = new FormData();
                formData.append('file', input.files[0]);

                $.ajax({
                    url: Routing.generate('pim_customentity_media_rest_post'),
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    async: false
                }).done(function (data) {
                    fieldValue = JSON.stringify(data);
                }).fail(function () {
                    self.errors.push({
                        code: self.fieldName,
                        message: _.__('pim_enrich.entity.product.error.upload'),
                        global: false
                    });
                });

                return fieldValue;
            },

            /**
             * Clear media field
             */
            clearField: function () {
                this.updateModel(null);
                this.render();
            }
        });
    });
