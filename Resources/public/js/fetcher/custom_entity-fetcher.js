/* global console */
'use strict';

define(
    ['jquery', 'underscore', 'backbone', 'routing', 'pim/base-fetcher'],
    function ($, _, Backbone, Routing, BaseFetcher) {
        return BaseFetcher.extend({
            /**
             * Fetch an element based on its identifier
             *
             * @param {string} customEntityName
             * @param {string} identifier
             * @param {Object} options
             *
             * @return {Promise}
             */
            fetch: function (customEntityName, identifier, options) {
                options = options || {};

                if (!(identifier in this.entityPromises) || false === options.cached) {
                    let deferred = $.Deferred();

                    if (this.options.urls.get) {
                        $.getJSON(
                            Routing.generate(
                                this.options.urls.get,
                                _.extend({
                                    customEntityName: customEntityName,
                                    id: identifier
                                }, options)
                            )
                        ).then(_.identity).done(function (entity) {
                            deferred.resolve(entity);
                        }).fail(function (promise, status, error) {
                            console.error('Error during fetching: ', error);

                            return deferred.reject(promise);
                        });
                    } else {
                        this.fetchAll().done(function (entities) {
                            const entity = _.findWhere(entities, {id: identifier});
                            if (entity) {
                                deferred.resolve(entity);
                            } else {
                                deferred.reject();
                            }
                        });
                    }

                    this.entityPromises[identifier] = deferred.promise();
                }

                return this.entityPromises[identifier];
            }
        });
    });
