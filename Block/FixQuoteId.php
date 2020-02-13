<?php

namespace BeeBots\AdminCustomerQuote\Block;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Model\Session\Quote;

/**
 * Class FixQuoteId
 *
 * @package BeeBots\AdminCustomerQuote\Block
 */
class FixQuoteId extends Template
{
    /** @var Quote */
    private $quoteSession;

    /**
     * EmailQuote constructor.
     *
     * @param Quote $quoteSession
     * @param Context $context
     * @param array $data
     */
    public function __construct(Quote $quoteSession, Context $context, array $data = [])
    {
        parent::__construct($context, $data);
        $this->quoteSession = $quoteSession;
    }

    /**
     * Function: getQuoteId
     *
     * @return int
     */
    public function getQuoteId()
    {
        return $this->quoteSession->getQuoteId();
    }
}
