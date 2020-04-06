<?php


namespace BeeBots\AdminCustomerQuote\Plugin;


use BeeBots\AdminCustomerQuote\Model\ResourceModel\CustomerQuoteResource;
use Magento\Backend\Model\Session\Quote;

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
     * GetPreviousQuoteIfAvailable constructor.
     *
     * @param CustomerQuoteResource $customerQuoteResource
     */
    public function __construct(CustomerQuoteResource $customerQuoteResource)
    {
        $this->customerQuoteResource = $customerQuoteResource;
    }

    /**
     * Function: beforeGetQuote
     *
     * @param Quote $quote
     */
    public function beforeGetQuote(Quote $quote)
    {
        if ($this->quoteIdAlreadyInitialized) {
            return;
        }
        $this->quoteIdAlreadyInitialized = true;
        // do not load previous quote if an order is being reordered
        if ($this->isReorder($quote)) {
            return;
        }
        // set latest quote id on session if customer and quote available
        $customerId = $quote->getCustomerId();
        if (! $customerId) {
            return;
        }
        $quoteId = $this->customerQuoteResource->getLatestQuoteIdOrNullForCustomer($customerId);
        if (! $quoteId) {
            return;
        }
        $quote->setQuoteId($quoteId);
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
