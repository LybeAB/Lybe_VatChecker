<?php

class Lybe_VatChecker_Model_Checkout_Onepage extends Mage_Checkout_Model_Type_Onepage
{
    public function saveBilling($data, $customerAddressId)
    {

        $checkVat = Mage::helper('customer')->checkVatNumber(
            $data['country_id'],
            $data['taxvat']
        );

        if(!$checkVat->getIsValid()){
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid VAT Number.'));
        }

        $returnValue = parent::saveBilling($data, $customerAddressId);

        return $returnValue;
    }
}
