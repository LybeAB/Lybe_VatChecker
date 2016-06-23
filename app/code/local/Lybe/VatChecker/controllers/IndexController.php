<?php
class Lybe_VatChecker_IndexController extends Mage_Core_Controller_Front_Action
{

    /**
     * Check whether vat is valid
     *
     * @return void
     */

    public function IndexAction() {

        $result = $this->_validatevat();

        $this->getResponse()->setBody((int)$result->getIsValid());

    }


    /**
     * Perform customer VAT ID validation
     *
     * @return Varien_Object
     */
    protected function _validatevat()
    {
        return Mage::helper('customer')->checkVatNumber(
            $this->getRequest()->getParam('country'),
            $this->getRequest()->getParam('vat')
        );
    }
}