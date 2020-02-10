<?php


namespace BeeBots\AdminCustomerQuote\Block\Adapted\Quote\Email\Items\Quote;


use Magento\Directory\Model\Currency;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class DefaultQuoteItemPrice
 *
 * @package BeeBots\AdminCustomerQuote\Block\Adapted\Quote\Email\Items\Quote
 */
class DefaultQuoteItemPrice extends \Magento\Framework\View\Element\Template
{
    /** @var Currency */
    private $currency;

    /**
     * DefaultQuoteItemPrice constructor.
     *
     * @param Currency $currency
     * @param Context $context
     * @param array $data
     */
    public function __construct(Currency $currency, Context $context, array $data = [])
    {
        parent::__construct($context, $data);
        $this->currency = $currency;
    }

    /**
     * Function: formatPrice
     *
     * @param $price
     *
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->currency->format($price);
    }
}
