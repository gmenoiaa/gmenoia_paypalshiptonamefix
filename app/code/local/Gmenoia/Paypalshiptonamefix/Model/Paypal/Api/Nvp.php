<?php

/**
 * 
 * @author geiser
 *
 */
class Gmenoia_Paypalshiptonamefix_Model_Paypal_Api_Nvp extends Mage_Paypal_Model_Api_Nvp {
	
	/**
	 * Create billing and shipping addresses basing on response data
	 *
	 * @param array $data        	
	 */
	protected function _exportAddressses($data) {
		$address = new Varien_Object ();
		Varien_Object_Mapper::accumulateByMap ( $data, $address, $this->_billingAddressMap );
		$address->setExportedKeys ( array_values ( $this->_billingAddressMap ) );
		$this->_applyStreetAndRegionWorkarounds ( $address );
		$this->setExportedBillingAddress ( $address );
		// assume there is shipping address if there is at least one field specific to shipping
		if (isset ( $data ['SHIPTONAME'] )) {
			$shippingAddress = clone $address;
			Varien_Object_Mapper::accumulateByMap ( $data, $shippingAddress, $this->_shippingAddressMap );
			$this->_applyStreetAndRegionWorkarounds ( $shippingAddress );
			// PayPal doesn't provide detailed shipping name fields, so the name will be overwritten
			$shippingAddress->addData ( $this->_processFullname ( $data ['SHIPTONAME'] ) );
			$this->setExportedShippingAddress ( $shippingAddress );
		}
	}
	/**
	 * Retrieves the name from the SHIPTONAME response value.
	 *
	 * @param string $shiptoname        	
	 */
	protected function _processFullname($shiptoname) {
		$parts = explode ( ' ', $shiptoname );
		
		$first = array_shift ( $parts );
		$last = array_pop ( $parts );
		$middle = trim ( implode ( ' ', $parts ) );
		
		$data = array (
				'firstname' => $first,
				'lastname' => $last,
				'middlename' => $middle 
		);
		
		Mage::log ( print_r ( $data, 1 ) );
		
		return $data;
	}
}


