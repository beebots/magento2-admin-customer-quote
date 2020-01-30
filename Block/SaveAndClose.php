<?php


namespace BeeBots\AdminCustomerQuote\Block;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Backend\Block\Widget\Context;
use Magento\Sales\Block\Adminhtml\Order\Create;

/**
 * Class SaveAndClose
 *
 * @package BeeBots\AdminCustomerQuote\Block
 */
class SaveAndClose extends Template
{
    /** @var ButtonList */
    private $buttonList;

    /** @var Create */
    private $createBlock;

    /**
     *
     * @param Context $context
     * @param Create $createBlock
     * @param array $data
     */
    public function __construct(Context $context, Create $createBlock, array $data = [])
    {
        parent::__construct($context, $data);
        $this->buttonList = $context->getButtonList();
        $this->buttonList->add(
            'save_close',
            [
                'label' => __('Save and Close'),
                'id' => 'save_and_close_quote',
            ],
            1,
            0,
            'toolbar'
        );
        $this->createBlock = $createBlock;
    }

    /**
     * Function: getCancelUrl
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->createBlock->getCancelUrl();
    }
}
