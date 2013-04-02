<?php
class Meanbee_Shippingrules_Test_Model_Rule_Condition_Cart_Count extends EcomDev_PHPUnit_Test_Case {
    /** @var Meanbee_Shippingrules_Model_Rule_Condition_Cart_Count */
    protected $_obj = null;

    public function setUp() {
        $this->_obj = Mage::getModel('meanship/rule_condition_cart_count');
    }

    public function tearDown() {
        $this->_obj = null;
    }

    /**
     * @test
     */
    public function testModelConstructed() {
        $this->assertInstanceOf('Meanbee_Shippingrules_Model_Rule_Condition_Cart_Count', $this->_obj);
    }

    /**
     * @test
     */
    public function testStandardConditionMethods() {
        $this->_obj->setData(array(
            'attribute'          => '',
            'operator'           => '',
            'value'              => '',
            'is_value_processed' => 0,
            'rule'               => Mage::getModel('meanship/rule')
        ));

        $this->assertNotEmpty($this->_obj->getAttributeName());
        $this->assertNotEmpty($this->_obj->getAttributeElement());
        $this->assertNotEmpty($this->_obj->getValidateObjectKey());
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function testValidate($rate_request, $operator, $value, $result) {
        $this->_obj->setData(array(
            'operator'           => $operator,
            'value'              => $value,
            'is_value_processed' => 0,
            'rule'               => Mage::getModel('meanship/rule')
        ));

        $this->assertEquals($result == 'true', $this->_obj->validate(new Varien_Object($rate_request)), "Operator: $operator, Value: $value, Request: " . json_encode($rate_request));
    }
}