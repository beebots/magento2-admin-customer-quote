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

    /** @var Quote */
    private $quoteSession;

    /** @var Quote */
    private $quote;

    /** @var bool */
    private $quoteIdAlreadyInitialized = false;

    /**
     * GetPreviousQuoteIfAvailable constructor.
     *
     * @param CustomerQuoteResource $customerQuoteResource
     */
    public function __construct(CustomerQuoteResource $customerQuoteResource, Quote $quote)
    {
        $this->customerQuoteResource = $customerQuoteResource;
        $this->quote = $quote;
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
}
