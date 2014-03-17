<?php
class Meanbee_Shippingrules_Model_Rule_Condition_Abstract extends Mage_Rule_Model_Condition_Abstract {

    protected $_arrayInputTypes = array();

    /**
     * Default operator input by type map getter
     *
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            $this->_defaultOperatorInputByType = array(
                'string'      => array('==', '!=', '{}', '!{}', '^', '$', '!^', '!$', '//'),
                'numeric'     => array('==', '!=', '>=', '>', '<=', '<', '()', '!()'),
                'date'        => array('==', '>=', '<='),
                'select'      => array('==', '!='),
                'boolean'     => array('==', '!='),
                'multiselect' => array('()', '!()'),
                'grid'        => array('()', '!()'),
            );
            $this->_arrayInputTypes = array('multiselect', 'grid');
        }

        return $this->_defaultOperatorInputByType;
    }

    /**
     * Default operator options getter
     * Provides all possible operator options
     *
     * @return array
     */
    public function getDefaultOperatorOptions()
    {
        if (null === $this->_defaultOperatorOptions) {
            $this->_defaultOperatorOptions = array(
                '=='  => Mage::helper('rule')->__('is'),
                '!='  => Mage::helper('rule')->__('is not'),
                '>='  => Mage::helper('meanship')->__('greater than or equal to'),
                '<='  => Mage::helper('meanship')->__('less than or equal to'),
                '>'   => Mage::helper('rule')->__('greater than'),
                '<'   => Mage::helper('rule')->__('less than'),
                '{}'  => Mage::helper('rule')->__('contains'),
                '!{}' => Mage::helper('rule')->__('does not contain'),
                '()'  => Mage::helper('rule')->__('is one of'),
                '!()' => Mage::helper('rule')->__('is not one of'),
                '^'   => Mage::helper('meanship')->__('begins with'),
                '$'   => Mage::helper('meanship')->__('ends with'),
                '!^'   => Mage::helper('meanship')->__('does not begin with'),
                '!$'   => Mage::helper('meanship')->__('does not end with'),
                '//'   => Mage::helper('meanship')->__('matches regex'),
            );
        }

        return $this->_defaultOperatorOptions;
    }

    /**
     * Magento < 1.6 does not have this method.
     *
     * @return bool
     */
    public function isArrayOperatorType() {
        $op = $this->getOperator();
        return $op === '()' || $op === '!()' || in_array($this->getInputType(), $this->_arrayInputTypes);
    }

    /**
     *
     * Magento < 1.6 does not have this method.
     *
     * @param $validatedValue
     * @param $value
     * @param bool $strict
     * @return bool
     */
    protected function _compareValues($validatedValue, $value, $strict = true) {
        if ($strict && is_numeric($validatedValue) && is_numeric($value)) {
            return $validatedValue == $value;
        } else {
            $validatePattern = preg_quote($validatedValue, '~');
            if ($strict) {
                $validatePattern = '^' . $validatePattern . '$';
            }
            return (bool)preg_match('~' . $validatePattern . '~iu', $value);
        }
    }

    /**
     * Magento 1.5 has an incompatible implementation of this method.
     *
     * @return array|string|int|float
     */
    public function getValueParsed()
    {
        if (!$this->hasValueParsed()) {
            $value = $this->getData('value');
            if ($this->isArrayOperatorType() && is_string($value)) {
                $value = preg_split('#\s*[,;]\s*#', $value, null, PREG_SPLIT_NO_EMPTY);
            }
            $this->setValueParsed($value);
        }
        return $this->getData('value_parsed');
    }

    /**
     * Validate product attrbute value for condition
     *
     * @param   mixed $validatedValue Value to validate against
     * @return  bool
     */
    public function validateAttribute($validatedValue)
    {
        if (is_object($validatedValue)) {
            return false;
        }

        /**
         * Value defined in the rule condition
         */
        $value = $this->getValueParsed();

        /**
         * Comparison operator
         */
        $op = $this->getOperator();

        // if operator requires array and it is not, or on opposite, return false
        if ($this->isArrayOperatorType() xor is_array($value)) {
            return false;
        }

        $result = false;

        switch ($op) {
            case '==': case '!=':
            if (is_array($value)) {
                if (is_array($validatedValue)) {
                    $result = array_intersect($value, $validatedValue);
                    $result = !empty($result);
                } else {
                    return false;
                }
            } else {
                if (is_array($validatedValue)) {
                    $result = count($validatedValue) == 1 && array_shift($validatedValue) == $value;
                } else {
                    $result = $this->_compareValues($validatedValue, $value);
                }
            }
            break;

            case '<=': case '>':
            if (!is_scalar($validatedValue)) {
                return false;
            } else {
                $result = $validatedValue <= $value;
            }
            break;

            case '>=': case '<':
            if (!is_scalar($validatedValue)) {
                return false;
            } else {
                $result = $validatedValue >= $value;
            }
            break;

            case '{}': case '!{}':
            if (is_scalar($validatedValue) && is_array($value)) {
                foreach ($value as $item) {
                    if (stripos($validatedValue,$item)!==false) {
                        $result = true;
                        break;
                    }
                }
            } elseif (is_array($value)) {
                if (is_array($validatedValue)) {
                    $result = array_intersect($value, $validatedValue);
                    $result = !empty($result);
                } else {
                    return false;
                }
            } else {
                if (is_array($validatedValue)) {
                    $result = in_array($value, $validatedValue);
                } else {
                    $result = $this->_compareValues($value, $validatedValue, false);
                }
            }
            break;

            case '()': case '!()':
            if (is_array($validatedValue)) {
                $result = count(array_intersect($validatedValue, (array)$value))>0;
            } else {
                $value = (array)$value;
                foreach ($value as $item) {
                    if ($this->_compareValues($validatedValue, $item)) {
                        $result = true;
                        break;
                    }
                }
            }
            break;

            case '^': case '!^':
            if (!is_scalar($validatedValue) || !is_string($validatedValue)) {
                return false;
            } else {
                $length = strlen($value);
                $result = (substr($validatedValue, 0, $length) === $value);
            }
            break;

            case '$': case '!$':
            if (!is_scalar($validatedValue) || !is_string($validatedValue)) {
                return false;
            } else {
                $length = strlen($value);

                if ($length == 0) {
                    return true;
                }

                $result = (substr($validatedValue, -$length) === $value);
            }
            break;
            case '//':
                if (Mage::helper('meanship')->isValidRegex($value)) {
                    $result = (bool)preg_match($value, $validatedValue);
                }
                break;
        }

        if ('!=' == $op || '>' == $op || '<' == $op || '!{}' == $op || '!()' == $op || '!^' == $op || '!$' == $op) {
            $result = !$result;
        }

        return $result;
    }
}
