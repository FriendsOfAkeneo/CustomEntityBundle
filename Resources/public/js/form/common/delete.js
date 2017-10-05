'use strict';

/**
 * Delete reference data for groups
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
define(
    [
        'pim/form/common/delete',
        'custom_entity/remover/reference-data',
        'pim/router',
        'oro/loading-mask',
        'oro/translator',
        'oro/messenger'
    ],
    function (DeleteForm, RefDataRemover, router, LoadingMask, __, messenger) {
        return DeleteForm.extend({
            remover: RefDataRemover,

            /**
             * Send a request to the backend in order to delete the element
             */
            doDelete() {
                const config = this.config;
                const loadingMask = new LoadingMask();
                loadingMask.render().$el.appendTo(this.getRoot().$el).show();

                this.removeEntity(this.getFormData())
                    .done(function () {
                        messenger.notify('success', __(this.config.trans.success));
                        router.redirectToRoute(
                            this.config.redirect,
                            {customEntityName: this.config.routeParams.customEntityName}
                        );
                    }.bind(this))
                    .fail(function (xhr) {
                        const message = xhr.responseJSON && xhr.responseJSON.message ?
                            xhr.responseJSON.message : __(config.trans.failed);

                        messenger.notify('error', message);
                    }.bind(this))
                    .always(function () {
                        loadingMask.hide().$el.remove();
                    });
            },

            removeEntity(params) {
                return this.remover.remove(
                    this.config.route,
                    this.config.routeParams.customEntityName,
                    params.id
                );
            }
        });
    }
);
