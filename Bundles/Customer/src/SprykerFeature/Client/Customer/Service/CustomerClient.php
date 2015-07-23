<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Client\Customer\Service;

use Generated\Shared\Customer\CustomerAddressInterface;
use Generated\Shared\Customer\CustomerInterface;
use Generated\Shared\Transfer\CustomerTransfer;
use SprykerEngine\Client\Kernel\Service\AbstractClient;

/**
 * @method CustomerDependencyContainer getDependencyContainer()
 */
class CustomerClient extends AbstractClient implements CustomerClientInterface
{

    /**
     * @param CustomerInterface $customerTransfer
     *
     * @return CustomerInterface
     */
    public function registerCustomer(CustomerInterface $customerTransfer)
    {
        return $this->getDependencyContainer()->createZedCustomerStub()->register($customerTransfer);
    }

    /**
     * @param CustomerInterface $customerTransfer
     *
     * @return CustomerInterface
     */
    public function confirmRegistration(CustomerInterface $customerTransfer)
    {
        return $this->getDependencyContainer()->createZedCustomerStub()->confirmRegistration($customerTransfer);
    }

    /**
     * @param CustomerInterface $customerTransfer
     *
     * @return CustomerInterface
     */
    public function forgotPassword(CustomerInterface $customerTransfer)
    {
        return $this->getDependencyContainer()->createZedCustomerStub()->forgotPassword($customerTransfer);
    }

    /**
     * @param CustomerInterface $customerTransfer
     *
     * @return CustomerInterface
     */
    public function restorePassword(CustomerInterface $customerTransfer)
    {
        return $this->getDependencyContainer()->createZedCustomerStub()->restorePassword($customerTransfer);
    }

    /**
     * @param CustomerInterface $customerTransfer
     *
     * @return CustomerInterface
     */
    public function deleteCustomer(CustomerInterface $customerTransfer)
    {
        return $this->getDependencyContainer()->createZedCustomerStub()->delete($customerTransfer);
    }

    /**
     * @return CustomerInterface
     */
    public function getCustomer()
    {
        $customerTransfer = $this->getDependencyContainer()->createSessionCustomerSession()->getCustomer();

        return $customerTransfer;
    }

    /**
     * @param CustomerInterface $customerTransfer
     *
     * @return CustomerInterface
     */
    public function setCustomer(CustomerInterface $customerTransfer)
    {
        $customerTransfer = $this->getDependencyContainer()->createSessionCustomerSession()->setCustomer($customerTransfer);

        return $customerTransfer;
    }

    /**
     * @param CustomerInterface $customerTransfer
     *
     * @return CustomerInterface
     */
    public function getCustomerByEmail(CustomerInterface $customerTransfer)
    {
        $customerTransfer = $this->getDependencyContainer()->createZedCustomerStub()->get($customerTransfer);

        return $customerTransfer;
    }

    /**
     * @param CustomerInterface $customerTransfer
     *
     * @return CustomerInterface
     */
    public function updateCustomer(CustomerInterface $customerTransfer)
    {
        return $this->getDependencyContainer()->createZedCustomerStub()->update($customerTransfer);
    }

    /**
     * @param CustomerInterface $customerTransfer
     *
     * @return CustomerInterface
     */
    public function login(CustomerInterface $customerTransfer)
    {
        $customerTransfer = $this->getCustomerByEmail($customerTransfer);
        $this->getDependencyContainer()->createSessionCustomerSession()->setCustomer($customerTransfer);

        return $customerTransfer;
    }

    /**
     * @return mixed
     */
    public function logout()
    {
        return $this->getDependencyContainer()->createSessionCustomerSession()->logout();
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->getDependencyContainer()->createSessionCustomerSession()->hasCustomer();
    }

    /**
     * @param CustomerAddressInterface $addressTransfer
     *
     * @return CustomerAddressInterface
     */
    public function getAddress(CustomerAddressInterface $addressTransfer)
    {
        return $this->getDependencyContainer()->createZedCustomerStub()->getAddress($addressTransfer);
    }

    /**
     * @param CustomerAddressInterface $addressTransfer
     *
     * @return CustomerAddressInterface
     */
    public function updateAddress(CustomerAddressInterface $addressTransfer)
    {
        return $this->getDependencyContainer()->createZedCustomerStub()->updateAddress($addressTransfer);
    }

    /**
     * @param CustomerAddressInterface $addressTransfer
     *
     * @return CustomerAddressInterface
     */
    public function createAddress(CustomerAddressInterface $addressTransfer)
    {
        return $this->getDependencyContainer()->createZedCustomerStub()->createAddress($addressTransfer);
    }

}