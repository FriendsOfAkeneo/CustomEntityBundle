'use strict';

/**
 * Module to remove reference data
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
define(
    [
        'jquery',
        'underscore',
        'routing',
        'oro/mediator'
    ], function ($, _, Routing, mediator) {
        return _.extend({}, {
            /**
             * Remove an entity
             *
             * @param {String}  route
             * @param {String}  customEntityName
             * @param {Integer} entityId
             *
             * @return {Promise}
             */
            remove(route, customEntityName, entityId) {
                return $.ajax({
                    type: 'DELETE',
                    url: this.getUrl(route, customEntityName, entityId)
                }).then(function (entity) {
                    mediator.trigger('pim_enrich:form:entity:post_remove', customEntityName);

                    return entity;
                }.bind(this));
            },

            /**
             * Remove an entity
             *
             * @param {String}  route
             * @param {String}  customEntityName
             * @param {Integer} entityId
             *
             * @return {Promise}
             */
            getUrl(route, customEntityName, entityId) {
                const routeParams = {
                    customEntityName: customEntityName,
                    id: entityId
                };

                return Routing.generate(route, routeParams);
            }
        });
    }
);
