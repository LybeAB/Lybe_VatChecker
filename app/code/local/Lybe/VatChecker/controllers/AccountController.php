<?php

/**
 * Customer account controller
 */
require_once 'Mage/Customer/controllers/AccountController.php';

class Lybe_VatChecker_AccountController extends Mage_Customer_AccountController {

    /**
     * Create customer account action
     */
    public function createPostAction()
    {
        $errUrl = $this->_getUrl('*/*/create', array('_secure' => true));

        if (!$this->_validateFormKey()) {
            $this->_redirectError($errUrl);
            return;
        }

        /*
         * Override check if VAt is valid
         */
        if ($this->getRequest()->isPost()) {
            if ($this->getRequest()->getParam('taxvat')){
                $checkVat = Mage::helper('customer')->checkVatNumber(
                    $this->getRequest()->getParam('country'),
                    $this->getRequest()->getParam('taxvat')
                );

                if(!$checkVat->getIsValid()){
                    $session = $this->_getSession();
                    $session->setCustomerFormData($this->getRequest()->getPost());
                    $session->addError($this->__("VAT Number is Invalid"));
                    $this->_redirectError($errUrl);
                    return;
                }
            }

        }


        /** @var $session Mage_Customer_Model_Session */
        $session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->_redirectError($errUrl);
            return;
        }

        $customer = $this->_getCustomer();

        try {
            $errors = $this->_getCustomerErrors($customer);

            if (empty($errors)) {
                $this->cleanPasswordsValidationData();
                $customer->save();
                $this->_dispatchRegisterSuccess($customer);
                $this->_successProcessRegistration($customer);
                return;
            } else {
                $this->_addSessionError($errors);
            }
        } catch (Mage_Core_Exception $e) {
            $session->setCustomerFormData($this->getRequest()->getPost());
            if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                $url = $this->_getUrl('customer/account/forgotpassword');
                $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
            } else {
                $message = $this->_escapeHtml($e->getMessage());
            }
            $session->addError($message);
        } catch (Exception $e) {
            $session->setCustomerFormData($this->getRequest()->getPost());
            $session->addException($e, $this->__('Cannot save the customer.'));
        }

        $this->_redirectError($errUrl);
    }

    /**
     * Clean password's validation data (password, password_confirmation)
     *
     * @return Mage_Customer_Model_Customer
     */
    public function cleanPasswordsValidationData()
    {
        $customer = $this->_getCustomer();
        $customer->setData('password', null);
        $customer->setData('password_confirmation', null);
        return $this;
    }




}