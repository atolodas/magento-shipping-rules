<?php
class Meanbee_Shippingrules_Calculator_Condition_Destination
	extends Meanbee_Shippingrules_Calculator_Condition_Abstract
{
	/**
	 * {@inheritdoc}
	 * @todo
	 * @implementation Meanbee_Shippingrules_Calculator_Condition_Abstract
	 * @return string [description]
	 */
	public function getCategory() {
		return 'Shipping Destination Conditions';
	}

	/**
	 * {@inheritdoc}
	 * @todo
	 * @implementation Meanbee_Shippingrules_Calculator_Condition_Abstract
	 * @return array[] [description]
	 */
	public function getVariables($context) {
		if (!isset($context['group']) || is_null($context)) {
			return array(
				'dest_country_id'    => array('label' => 'Shipping Country',       'type' => array('enumerated'), 'options' => array()),
				'dest_country_group' => array('label' => 'Shipping Country Group', 'type' => array('enumerated'), 'options' => array()),
				'dest_region_id'     => array('label' => 'Shipping State',         'type' => array('enumerated'), 'options' => array())
			);
		}
		return array();
	}

}