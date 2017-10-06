'use strict';
/**
 * Label extension
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
define(
    ['pim/form/common/label', 'pim/user-context', 'pim/i18n'],
    function (BaseForm, UserContext, i18n) {
        return BaseForm.extend({
            /**
             * {@inheritdoc}
             */
            getLabel() {
                const data = this.getFormData();
                let labels = [];

                if (undefined !== data.labels) {
                    labels = data.labels;
                }

                return i18n.getLabel(
                    labels,
                    UserContext.get('catalogLocale'),
                    data.code
                );
            }
        });
    }
);
