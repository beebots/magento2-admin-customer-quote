<?php


namespace BeeBots\AdminCustomerQuote\Observer;


use Magento\Backend\Model\Session\Quote;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\AdminOrder\Create;

/**
 * Class SaveCustomerBeforeClose
 *
 * @package BeeBots\AdminCustomerQuote\Observer
 */
class SaveCustomerBeforeClose implements ObserverInterface
{
    /** @var Create */
    private $adminOrderCreate;

    /** @var CustomerRepository */
    private $customerRepository;

    /**
     * SaveCustomerBeforeClose constructor.
     *
     * @param Create $adminOrderCreate
     * @param CustomerRepository $customerRepository
     */
    public function __construct(Create $adminOrderCreate, CustomerRepository $customerRepository)
    {
        $this->adminOrderCreate = $adminOrderCreate;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var RequestInterface $request */
        $request = $observer->getData('request_model');
        /** @var Quote $quoteSession */
        $quoteSession = $observer->getData('session');
        $store = $quoteSession->getStore();
        $isPreCloseRequest = $request->getParam('beePreCloseRequest', false);
        if (! $isPreCloseRequest) {
            return;
        }
        $quote = $quoteSession->getQuote();
        if ($quote->getCustomer()->getId()) {
            return;
        }
        /* Inspired from \Magento\Sales\Model\AdminOrder\Create::_prepareCustomer (called on create order) */
        /** @var CustomerInterface $customer */
        $customer = $quote->getCustomer();
        $customerBillingAddressDataObject = $this->adminOrderCreate->getBillingAddress()->exportCustomerAddress();
        $customer->setSuffix($customerBillingAddressDataObject->getSuffix())
            ->setFirstname($customerBillingAddressDataObject->getFirstname())
            ->setLastname($customerBillingAddressDataObject->getLastname())
            ->setMiddlename($customerBillingAddressDataObject->getMiddlename())
            ->setPrefix($customerBillingAddressDataObject->getPrefix())
            ->setStoreId($store->getId())
            ->setWebsiteId($store->getWebsiteId())
            ->setEmail($this->getEmailFromRequest($request));
        $customer = $this->customerRepository->save($customer);
        $quote->setCustomer($customer);
    }

    /**
     * Function: getEmailFromRequest
     *
     * @param $request
     *
     * @return string|null
     */
    private function getEmailFromRequest($request)
    {
        $order = $request->getParam('order');
        return isset($order['account']['email']) ? $order['account']['email'] : null;
    }
}
