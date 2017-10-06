'use strict';

define(
    [
        'jquery',
        'underscore',
        'oro/translator',
        'backbone',
        'routing',
        'pim/form/common/creation/modal',
        'oro/loading-mask',
        'pim/router',
        'oro/messenger'
    ],
    function (
        $,
        _,
        __,
        Backbone,
        Routing,
        BaseForm,
        LoadingMask,
        router,
        messenger
    ) {
        return BaseForm.extend({
            /**
             * {@inheritDoc}
             */
            confirmModal(modal, deferred) {
                this.save().done(entity => {
                    modal.close();
                    modal.remove();
                    deferred.resolve();

                    const routerParams = {
                        customEntityName: entity.customEntityName,
                        id: entity.id
                    };

                    messenger.notify('success', __(this.config.successMessage));

                    router.redirectToRoute(
                        this.config.editRoute,
                        routerParams
                    );
                });
            },

            /**
             * {@inheritDoc}
             */
            save() {
                this.validationErrors = {};

                const loadingMask = new LoadingMask();
                this.$el.empty().append(loadingMask.render().$el.show());

                const data = $.extend(this.getFormData(),
                    this.config.defaultValues || {});

                return $.ajax({
                    url: Routing.generate(this.config.postUrl.route, {
                        'customEntityName': this.config.postUrl.parameters.customEntityName
                    }),
                    type: 'POST',
                    data: JSON.stringify(data)
                }).fail(function (response) {
                    const errors = response.responseJSON ?
                        this.normalize(response.responseJSON) : [{message: __('error.common')}];
                    this.validationErrors = errors;
                    this.render();
                }.bind(this))
                    .always(() => loadingMask.remove());
            }
        });
    }
);
