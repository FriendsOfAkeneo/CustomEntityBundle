define([
    'pim/product-edit-form/locale-switcher',
    'underscore',
    'pim/i18n',
    'oro/translator',
    'pim/user-context',
], function (LocalSwitcher, _, i18n, __, UserContext) {
    "use strict";

    return LocalSwitcher.extend({
        currentLocaleCode: null,

        /**
         * {@inheritdoc}
         */
        configure: function () {
            this.update(UserContext.get('uiLocale'));
        },

        /**
         * Method triggered on the 'change locale' event
         *
         * @param {Object} event
         */
        changeLocale: function (event) {
            this.update(event.target.dataset.locale);
        },

        /**
         * @param {String} localeCode
         */
        update: function(localeCode) {
            this.currentLocaleCode = localeCode;
            this.getRoot().trigger('custom_entity:form:custom:translatable:switcher:change', {localeCode: localeCode});
            this.render();
        },

        /**
         * {@inheritdoc}
         */
        render: function () {
            this.getDisplayedLocales()
                .done(function (locales) {

                    if (!this.currentLocaleCode) {
                        this.currentLocaleCode = _.first(locales).code;
                    }

                    this.$el.html(
                        this.template({
                            locales: locales,
                            currentLocale: _.findWhere(locales, {code: this.currentLocaleCode}),
                            i18n: i18n,
                            displayInline: this.displayInline,
                            displayLabel: this.displayLabel,
                            label: __('pim_enrich.entity.product.meta.locale')
                        })
                    );
                    this.delegateEvents();
                }.bind(this));

            return this;
        },
    });
});