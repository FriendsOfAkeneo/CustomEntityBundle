define([
    'pim/common/properties/translation',
    'custom_entity/template/localizable/text',
    'pim/form',
    'underscore',
    'jquery',
    'pim/user-context',
], function (Translation, template, BaseForm, _, $, UserContext) {
    "use strict";

    return Translation.extend({
        template: _.template(template),
        localeCode: null,

        /**
         * {@inheritdoc}
         */
        configure: function () {
            this.listenTo(
                this.getRoot(),
                'pim_enrich:form:entity:pre_save',
                this.onPreSave
            );

            this.listenTo(
                this.getRoot(),
                'pim_enrich:form:entity:bad_request',
                this.onValidationError
            );

            this.listenTo(
                this.getRoot(),
                'pim_enrich:form:entity:locales_updated',
                this.onLocalesUpdated.bind(this)
            );

            this.listenTo(
                this.getRoot(),
                'custom_entity:form:custom:translatable:switcher:change',
                this.onLocaleChanged.bind(this)
            );

            return $.when(
                this.getLocales(true)
                    .then(function (locales) {
                        this.locales = locales;
                        this.localeCode = UserContext.get('uiLocale');
                    }.bind(this)),
                BaseForm.prototype.configure.apply(this, arguments)
            );
        },

        /**
         * {@inheritdoc}
         */
        render: function () {
            this.$el.html(this.template({
                model: this.getFormData(),
                locales: this.locales,
                currentLocaleCode: this.localeCode,
                errors: this.validationErrors,
                label: this.config.label,
                fieldName: this.config.fieldName,
                isReadOnly: this.isReadOnly()
            }));

            this.delegateEvents();
            this.renderExtensions();
        },

        /**
         * @param {{}} context
         */
        onLocaleChanged: function (context) {
            this.localeCode = context.localeCode;
            this.render();
        },

        /**
         * @param {Object} event
         */
        updateModel: function (event) {
            var data = this.getFormData();

            if (Array.isArray(data[this.config.fieldName])) {
                data[this.config.fieldName] = {};
            }

            data[this.config.fieldName][event.target.dataset.locale] = event.target.value;

            this.setData(data);
        },
    });
});