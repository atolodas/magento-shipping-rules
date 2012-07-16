<?php
if (version_compare(Mage::getVersion(), '1.6', '>=')) {
    abstract class Meanbee_Shippingrules_Model_Resource_Rule_Collection_Abstract extends Mage_Rule_Model_Resource_Rule_Collection_Abstract {}
} else {
    abstract class Meanbee_Shippingrules_Model_Resource_Rule_Collection_Abstract extends Mage_Rule_Model_Mysql4_Rule_Collection {
        /**
         * This method appears to be incorrectly implemented in version 1.5.1.0, it calls core/rule_environment instead
         * of rule/environment.
         *
         * @return Mage_Rule_Model_Environment|Mage_Rule_Model_Resource_Rule_Collection_Abstract
         */
        public function getEnv() {
            if (!$this->_env) {
                $this->_env = Mage::getModel('rule/environment');
                $this->_env->collect();
            }
            return $this->_env;
        }
    }
}