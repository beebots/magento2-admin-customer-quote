<?php

namespace BeeBots\AdminCustomerQuote\Block\Adapted\Quote\Email\Items;

use Magento\Directory\Model\Currency;
use Magento\Framework\View\Element\Template\Context;
use Magento\Tax\Block\Sales\Order\Tax as MagentoTax;
use Magento\Tax\Model\Config;

class Tax extends MagentoTax
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
    public function __construct(
        Currency $currency,
        Context $context,
        Config $taxConfig,
        array $data = []
    ) {
        parent::__construct($context, $taxConfig, $data);
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

    /**
     * Initialize all order totals relates with tax
     *
     * @return \Magento\Tax\Block\Sales\Order\Tax
     */
    public function initTotals()
    {
        /** @var $parent \Magento\Sales\Block\Adminhtml\Order\Invoice\Totals */
        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $this->_source = $parent->getSource();

        $store = $this->getStore();
        $allowTax = $this->_source->getShippingAddress()->getTaxAmount() > 0 || $this->_config->displaySalesZeroTax(
            $store
        );
        $grandTotal = (double)$this->_source->getGrandTotal();
        if (! $grandTotal || $allowTax && ! $this->_config->displaySalesTaxWithGrandTotal($store)) {
            $this->_addTax();
        }

        $this->_initSubtotal();
        $this->_initShipping();
        $this->_initDiscount();
        $this->_initGrandTotal();
        return $this;
    }
}
