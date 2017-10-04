'use strict';

/**
 * Module to save an entity
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
define(
    ['underscore', 'pim/saver/entity-saver', 'routing'],
    function (_, BaseSaver, Routing) {
        return _.extend({}, BaseSaver, {
            routeParams: {},

            /**
             * {@inheritdoc}
             */
            getUrl(identifier) {
                const defaultParams = {
                    id: identifier
                };

                const routeParams = Object.assign({}, this.getRouteParams(), defaultParams);

                return Routing.generate(this.url, routeParams);
            },

            /**
             * @param {Object} routeParams
             */
            setRouteParams(routeParams) {
                this.routeParams = routeParams;

                return this;
            },

            /**
             * return {Object]
             */
            getRouteParams() {
                return this.routeParams;
            }
        });
    }
);
