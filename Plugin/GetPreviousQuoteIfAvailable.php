<?php


namespace BeeBots\AdminCustomerQuote\Plugin;


use BeeBots\AdminCustomerQuote\Model\ResourceModel\CustomerQuoteResource;
use Magento\Backend\Model\Session\Quote;
use Magento\Customer\Model\ResourceModel\CustomerRepository;

/**
 * Class GetPreviousQuoteIfAvailable
 *
 * @package BeeBots\AdminCustomerQuote\Plugin
 */
class GetPreviousQuoteIfAvailable
{
    /** @var CustomerQuoteResource */
    private $customerQuoteResource;

    /** @var bool */
    private $quoteIdAlreadyInitialized = false;

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * GetPreviousQuoteIfAvailable constructor.
     *
     * @param CustomerQuoteResource $customerQuoteResource
     * @param CustomerRepository $customerRepository
     */
    public function __construct(CustomerQuoteResource $customerQuoteResource, CustomerRepository $customerRepository)
    {
        $this->customerQuoteResource = $customerQuoteResource;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Function: beforeGetQuote
     *
     * @param Quote $quoteSession
     */
    public function beforeGetQuote(Quote $quoteSession)
    {
        if ($this->quoteIdAlreadyInitialized) {
            return;
        }
        $this->quoteIdAlreadyInitialized = true;
        // do not load previous quote if an order is being reordered
        if ($this->isReorder($quoteSession)) {
            return;
        }
        // set latest quote id on session if customer and quote available
        $customerId = $quoteSession->getCustomerId();
        if (! $customerId) {
            return;
        }
        $quoteId = $this->customerQuoteResource->getLatestQuoteIdOrNullForCustomer($customerId);
        if (! $quoteId) {
            return;
        }
        $quoteSession->setQuoteId($quoteId);
        $customer = $this->customerRepository->getById($customerId);
        $quote = $quoteSession->getQuote();
        if ($customer->getGroupId() !== $quote->getCustomerGroupId()) {
            $quote->setCustomerGroupId($customer->getGroupId());
        }
    }

    /**
     * Function: isReorder
     *
     * @param Quote $quote
     *
     * @return mixed
     */
    private function isReorder(Quote $quote)
    {
        return $quote->getData('reordered');
    }
}
