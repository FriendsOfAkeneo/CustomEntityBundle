'use strict';

/**
 * Save extension for simple entity types
 *
 * @author    Tamara Robichet <tamara.robichet@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
define(
    [
        'underscore',
        'pim/form/common/save-form',
        'custom_entity/form/common/entity-saver',
        'pim/field-manager',
        'pim/user-context',
        'pim/router'
    ],
    function (_,
              BaseSave,
              EntitySaver,
              FieldManager,
              UserContext,
              router) {
        return BaseSave.extend({
            /**
             * {@inheritdoc}
             */
            save() {
                const excludedProperties = _.union(this.config.excludedProperties, ['meta']);
                const entity = _.omit(this.getFormData(), excludedProperties);

                const notReadyFields = FieldManager.getNotReadyFields();

                if (0 < notReadyFields.length) {
                    const catalogLocale = UserContext.get('catalogLocale');
                    const fieldLabels = this.getFieldLabels(notReadyFields, catalogLocale);

                    return this.showFlashMessage(this.notReadyMessage, fieldLabels);
                }

                this.showLoadingMask();
                this.getRoot().trigger('pim_enrich:form:entity:pre_save');

                return EntitySaver
                    .setUrl(this.config.url)
                    .setRouteParams(this.config.route_params)
                    .save(entity.id, entity, this.config.method || 'POST')
                    .then(function (data) {
                        this.postSave();
                        this.setData(data);
                        this.getRoot().trigger('pim_enrich:form:entity:post_fetch', data);

                        if (this.config.redirectAfter) {
                            const params = Object.assign(
                                {id: entity.id},
                                this.config.route_params
                            );

                            router.redirectToRoute(this.config.redirectAfter, params);
                        }
                    }.bind(this))
                    .fail(this.fail.bind(this))
                    .always(this.hideLoadingMask.bind(this));
            }
        });
    }
);
