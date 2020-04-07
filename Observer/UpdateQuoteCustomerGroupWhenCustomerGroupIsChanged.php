<?php

namespace BeeBots\AdminCustomerQuote\Observer;

use BeeBots\AdminCustomerQuote\Model\ResourceModel\CustomerQuoteResource;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\QuoteRepository;
use Psr\Log\LoggerInterface;
use Throwable;

class UpdateQuoteCustomerGroupWhenCustomerGroupIsChanged implements ObserverInterface
{
    /** @var CustomerInterface */
    private $customerBeforeSave;

    /** @var CustomerRepository */
    private $customerRepository;

    /** @var ManagerInterface */
    private $eventManager;

    /** @var LoggerInterface */
    private $logger;

    /** @var CustomerQuoteResource */
    private $customerQuoteResource;

    /** @var QuoteRepository */
    private $quoteRepository;

    /**
     * UpdateQuoteCustomerGroupWhenCustomerGroupIsChanged constructor.
     *
     * @param CustomerInterface $customerBeforeSave
     * @param CustomerRepository $customerRepository
     * @param ManagerInterface $eventManager
     * @param LoggerInterface $logger
     * @param CustomerQuoteResource $customerQuoteResource
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(
        CustomerInterface $customerBeforeSave,
        CustomerRepository $customerRepository,
        ManagerInterface $eventManager,
        LoggerInterface $logger,
        CustomerQuoteResource $customerQuoteResource,
        QuoteRepository $quoteRepository
    ) {
        $this->customerBeforeSave = $customerBeforeSave;
        $this->customerRepository = $customerRepository;
        $this->eventManager = $eventManager;
        $this->logger = $logger;
        $this->customerQuoteResource = $customerQuoteResource;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * This observer is called on 2 events 'adminhtml_customer_prepare_save' and 'adminhtml_customer_save_after'
     *
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        try {
            /** @noinspection PhpUndefinedMethodInspection */
            /** @var CustomerInterface $customer */
            $customer = $observer->getEvent()->getCustomer();
            $eventName = $observer->getEvent()->getName();
            switch ($eventName) {
                case 'adminhtml_customer_prepare_save':
                    $this->customerBeforeSave = $this->getPreviousCustomerData($customer);
                    break;
                case 'adminhtml_customer_save_after':
                    if ($this->customerBeforeSave
                        && $this->customerBeforeSave->getGroupId() !== $customer->getGroupId()) {
                        // save new customer group to quote
                        $customerQuote = $this->customerQuoteResource->getLatestQuoteIdOrNullForCustomer(
                            $customer->getId()
                        );
                        if ($customerQuote) {
                            $quote = $this->quoteRepository->get($customerQuote);
                            $quote->setCustomerGroupId($customer->getGroupId());
                            $quote->setTriggerRecollect(1);
                            $quote->save();
                        }
                    }
            }
        } catch (Throwable $t) {
            $this->logger->error('Error updating quote if customer group has changed', ['exception' => $t]);
        }
    }

    /**
     * @param CustomerInterface $customer
     *
     * @return CustomerInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getPreviousCustomerData(CustomerInterface $customer)
    {
        $customerId = $customer->getId();
        if (! $customerId) {
            return null;
        }
        return $this->customerRepository->getById($customerId);
    }
}
