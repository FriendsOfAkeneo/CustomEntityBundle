'use strict';

define(
    [
        'underscore',
        'oro/translator',
        'pim/controller/front',
        'pim/form-builder',
        'pim/fetcher-registry'
    ],
    function (_, __, BaseController, FormBuilder, FetcherRegistry) {
        return BaseController.extend({
            /**
             * {@inheritdoc}
             */
            renderForm: function (route) {
                return FetcherRegistry.getFetcher('custom_entity')
                    .fetch(route.params.customEntityName, route.params.id, {cached: false})
                    .then((normalizedEntity) => {
                        const formExtension = normalizedEntity.meta.form;

                        return FormBuilder.build(formExtension)
                            .then((form) => {
                                this.on('pim:controller:can-leave', function (event) {
                                    form.trigger('pim_enrich:form:can-leave', event);
                                });
                                form.setData(normalizedEntity);
                                form.trigger('pim_enrich:form:entity:post_fetch', normalizedEntity);
                                form.setElement(this.$el).render();

                                return form;
                            });
                    });
            }
        });
    }
);
