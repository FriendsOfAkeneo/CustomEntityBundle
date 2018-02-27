'use strict';

/**
 * Reference data select field
 *
 * @author    Kevin Rademan <kevin@versa.co.za>
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
define(
    [
        'jquery',
        'underscore',
        'oro/translator',
        'pim/fetcher-registry',
        'pim/form/common/fields/select'
    ],
    function ($, _, __, FetcherRegistry, SelectField ) {
        return SelectField.extend({
            /**
             * {@inherit}
             */
            configure() {
                let fetcher = null;
                if (this.config.isCustomEntity) {
                    fetcher = FetcherRegistry.getFetcher('custom_entity').fetchAllByType(this.config.entityName);
                } else {
                    fetcher = FetcherRegistry.getFetcher(this.config.entityName).fetchAll();
                }
                return $.when(
                    fetcher,
                    SelectField.prototype.configure.apply(this, arguments)
                ).then(function (items) {
                    if (_.isEmpty(items)) {
                        this.config.readOnly = true;
                        this.config.choices = {
                            'NO OPTION': __('pim_custom_entity.import.csv.entity_name.no_reference_data')
                        };
                    } else {
                        let choices = {};
                        const nameProp = this.config.choiceNameField;
                        const valueProp = this.config.choiceValueField;
                        items.forEach(function (item) {
                            choices[item[nameProp]] = item[valueProp];
                        });
                        this.config.choices = choices;
                    }
                }.bind(this));
            },

            /**
             * {@inheritdoc}
             */
            getFieldValue: function (field) {
                const value = $(field).val() || null;

                return this.config.isMultiple && null === value ? [] : value;
            }
        });
    });
