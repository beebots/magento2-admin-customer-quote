<?php


namespace BeeBots\AdminCustomerQuote\Block\Adapted\Quote\Email\Items\Quote;

use Magento\Quote\Model\Quote\Item as QuoteItem;
use function array_merge;
use function is_array;
use function sprintf;

/**
 * Class DefaultQuote adapted from \Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder
 *
 * @package BeeBots\AdminCustomerQuote\Block\Adapted\Quote\Email\Items\Quote
 */
class DefaultQuote extends \Magento\Framework\View\Element\Template
{
    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->getItem()->getQuote();
    }

    /**
     * @return array
     */
    public function getItemOptions()
    {
        $result = [];
        if ($options = $this->getItem()->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }

        return $result;
    }

    /**
     * @param string|array $value
     *
     * @return string
     */
    public function getValueHtml($value)
    {
        if (is_array($value)) {
            return sprintf(
                    '%d',
                    $value['qty']
                ) . ' x ' . $this->escapeHtml(
                    $value['title']
                ) . " " . $this->getItem()->getQuote()->formatPrice(
                    $value['price']
                );
        } else {
            return $this->escapeHtml($value);
        }
    }

    /**
     * @param mixed $item
     *
     * @return mixed
     */
    public function getSku($item)
    {
        if ($item->getProductOptionByCode('simple_sku')) {
            return $item->getProductOptionByCode('simple_sku');
        } else {
            return $item->getSku();
        }
    }

    /**
     * Return product additional information block
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    public function getProductAdditionalInformationBlock()
    {
        return $this->getLayout()->getBlock('additional.product.info');
    }

    /**
     * Get the html for item price
     *
     * @param QuoteItem $item
     *
     * @return string
     */
    public function getItemPrice(QuoteItem $item)
    {
        $block = $this->getLayout()->getBlock('item_price');
        $block->setItem($item);
        return $block->toHtml();
    }
}
