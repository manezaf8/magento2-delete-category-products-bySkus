define([
    'jquery',
    'Magento_Ui/js/form/element/abstract',
    'Magento_Ui/js/lib/validation/validator',
], function ($, Abstract, validator) {
    'use strict';

    return Abstract.extend({
        initialize: function () {
            var self = this;
            this._super();

            validator.addRule(
                'validate-duplicate-skus',
                function (value, element) {
                    var skus = value.split(',');
                    var checkedSkus = [];

                    for (var i = 0; i < skus.length; i++) {
                        if (checkedSkus.includes(skus[i])) {
                            return false;
                        } else {
                            checkedSkus.push(skus[i]);
                        }
                    }

                    return true;
                },
                $.mage.__('Please remove duplicate SKUs')
            );

            return this;
        }
    });
});
