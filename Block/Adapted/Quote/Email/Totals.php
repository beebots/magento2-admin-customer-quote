<?php


namespace BeeBots\AdminCustomerQuote\Block\Adapted\Quote\Email;

use Magento\Directory\Model\Currency;
use Magento\Framework\DataObject;
use \Magento\Sales\Block\Order\Totals as OrderTotals;
use function is_numeric;

/**
 * Class Totals
 *
 * @package BeeBots\AdminCustomerQuote\Block\Adapted\Quote\Email
 */
class Totals extends OrderTotals
{
    /** @var Currency */
    private $currency;

    /**
     * Totals constructor.
     *
     * @param Currency $currency
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        Currency $currency,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->currency = $currency;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $registry, $data);
    }

    /**
     * Initialize order totals array
     *
     * @return $this
     */
    protected function _initTotals()
    {
        $source = $this->getSource();

        $this->_totals = [];
        $this->_totals['subtotal'] = new DataObject(
            ['code' => 'subtotal', 'value' => $source->getSubtotal(), 'label' => __('Subtotal')]
        );

        /**
         * Add shipping
         */
        $shippingAddress = $source->getShippingAddress();
        if (! $source->getIsVirtual() && $shippingAddress && (is_numeric($shippingAddress->getShippingAmount()))) {
            $this->_totals['shipping'] = new DataObject(
                [
                    'code' => 'shipping',
                    'field' => 'shipping_amount',
                    'value' => $shippingAddress->getShippingAmount(),
                    'label' => __('Shipping & Handling'),
                ]
            );
        }

        /**
         * Add discount
         */
        if (is_numeric($this->getSource()->getSubtotalWithDiscount()) && (double)$this->getSource()->getSubtotalWithDscount() < (double)$this->getSource()->getSubtotal() ) {
            $discountLabel = __('Discount');

            $this->_totals['discount'] = new DataObject(
                [
                    'code' => 'discount',
                    'field' => 'discount_amount',
                    'value' => (double)$this->getSource()->getSubtotalWithDiscount() - (double)$this->getSource()->getSubtotal(),
                    'label' => $discountLabel,
                ]
            );
        }

        $this->_totals['grand_total'] = new DataObject(
            [
                'code' => 'grand_total',
                'field' => 'grand_total',
                'strong' => true,
                'value' => $source->getGrandTotal(),
                'label' => __('Grand Total'),
            ]
        );

        return $this;
    }

    /**
     * Format total value based on order currency
     *
     * @param DataObject $total
     *
     * @return  string
     */
    public function formatValue($total)
    {
        if (! $total->getIsFormated()) {
            return $this->currency->format($total->getValue());
        }
        return $total->getValue();
    }
}
