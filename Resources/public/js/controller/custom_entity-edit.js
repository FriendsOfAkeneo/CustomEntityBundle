'use strict';

define(
    [
        'underscore',
        'oro/translator',
        'pim/controller/front',
        'pim/form-builder',
        'pim/fetcher-registry',
        'pim/user-context',
        'pim/dialog',
        'pim/page-title'
    ],
    function (_, __, BaseController, FormBuilder, FetcherRegistry, UserContext, Dialog, PageTitle) {
        return BaseController.extend({
            /**
             * {@inheritdoc}
             */
            renderForm: function (route) {
                return FetcherRegistry.getFetcher('custom_entity')
                    .fetch(route.params.customEntityName, route.params.id, {cached: false})
                    .then((normalizedEntity) => {
                        return createForm.call(
                            this,
                            this.$el,
                            normalizedEntity.data,
                            normalizedEntity.meta.form
                        );
                    });

                function createForm(domElement, entity, formExtension) {
                    return FormBuilder.build(formExtension)
                        .then((form) => {
                            this.on('pim:controller:can-leave', function (event) {
                                form.trigger('pim_enrich:form:can-leave', event);
                            });
                            form.setData(entity);
                            form.trigger('pim_enrich:form:entity:post_fetch', entity);
                            form.setElement(domElement).render();

                            return form;
                        });
                }
            }
        });
    }
);
