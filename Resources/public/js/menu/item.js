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
        'pim/router',
        'routing'
    ],
    function (BaseForm, router, Routing) {
        return BaseForm.extend({

            /**
             * {@inheritdoc}
             */
            render: function () {
                this.$el.empty().append(this.template({
                    title: this.getLabel(),
                    url: Routing.generateHash(this.getRoute(), this.getRouteParams()),
                    active: this.active
                }));

                this.delegateEvents();
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
