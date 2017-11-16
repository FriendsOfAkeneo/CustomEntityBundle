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
    function (
        $,
        _,
        __,
        FetcherRegistry,
        SelectField,
    ) {
        return SelectField.extend({
            /**
             * {@inherit}
             */
            configure: function () {
                let fetcher = null;
                if(this.config.isCustomEntity) {
                    fetcher = FetcherRegistry
                                .getFetcher('custom_entity')
                                .fetchAllByType(this.config.entityName);
                } else {
                    fetcher = FetcherRegistry
                                .getFetcher(this.config.entityName)
                                .fetchAll();
                }
                return $.when(
                    fetcher,
                    SelectField.prototype.configure.apply(this, arguments)
                ).then(function (items) {
                    let config = this.config;
                    if (_.isEmpty(items)) {
                        this.config.readOnly = true;
                        this.config.choices = {
                            'NO OPTION': __('pim_custom_entity.import.csv.entity_name.no_reference_data')
                        };
                    } else {
                        let choices = {};
                        let nameProp = this.config.choiceNameField;
                        let valueProp = this.config.choiceValueField;
                        items.forEach(function(item) {
                            let entity = config.isCustomEntity ? item.data : item;
                            choices[entity[nameProp]] = entity[valueProp];
                        });
                        this.config.choices = choices;
                    }
                }.bind(this));
            }
        });
    });
