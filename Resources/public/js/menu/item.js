'use strict';

/**
 * Custom entity extension for menu
 *
 * @author JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
define(
    [
        'pim/menu/item',
        'pim/router'
    ],
    function (BaseForm, router) {
        return BaseForm.extend({
            /**
             * {@inheritdoc}
             */
            configure: function () {
                this.trigger('pim_menu:column:register_navigation_item', {
                    code: this.getRoute(),
                    label: this.getLabel(),
                    position: this.position
                });

                BaseForm.prototype.configure.apply(this, arguments);
            },

            /**
             * {@inheritDoc}
             */
            redirect: function (event) {
                if (!_.has(event, 'extension') || event.extension === this.code) {
                    router.redirectToRoute(this.getRoute(), this.getRouteParams());
                }
            },

            /**
             * Returns the route params of the tab.
             *
             * @returns {object|undefined}
             */
            getRouteParams: function () {
                return {
                    'customEntityName': this.config.custom_entity_name
                };
            }

        });
    });
