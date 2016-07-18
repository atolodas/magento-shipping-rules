'use strict';
(function (ShippingRules) {

    ShippingRules.Register.term = new ShippingRules.Register;
    ShippingRules.Register.term.add = function (key, child) {
        if (!this.has(key) && child.prototype instanceof ShippingRules.Term) {
            this.children[key] = child;
        }
        return this;
    };

})(Meanbee.ShippingRules);