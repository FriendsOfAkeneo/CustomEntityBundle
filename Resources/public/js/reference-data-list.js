'use strict';

/**
 * Reference data list fetcher
 *
 * @author    Anaël CHARDAN <anael.chardan@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
define([
    'jquery',
    'underscore',
    'oro/translator',
    'pim/fetcher-registry',
    'pim/job/common/edit/field/select'
], function (
    $,
    _,
    __,
    FetcherRegistry,
    SelectField
) {
    return SelectField.extend({
        /**
         * {@inherit}
         */
        configure: function () {
            return $.when(
                FetcherRegistry.getFetcher('reference-data-list').fetchAll(),
                SelectField.prototype.configure.apply(this, arguments)
            ).then(function (referenceDataList) {
                if (_.isEmpty(referenceDataList)) {
                    this.config.readOnly = true;
                    this.config.options = {'NO OPTION': __('pim_custom_entity.import.csv.entity_name.no_reference_data')};
                } else {
                    this.config.options = referenceDataList;
                }
            }.bind(this));
        }
    });
});
