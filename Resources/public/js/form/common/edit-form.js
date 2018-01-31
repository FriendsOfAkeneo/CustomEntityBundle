/**
 * @author    Mathias Métayer <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
'use strict';

define([
    'pim/form/common/edit-form'
], function (
    BaseEditForm
) {
    return BaseEditForm.extend({
        /**
         * {@inheritdoc}
         */
        configure: function () {
            this.listenTo(this.getRoot(), 'pim_enrich:form:entity:post_fetch', this.render);

            return BaseEditForm.prototype.configure.apply(this, arguments);
        }
    });
});
