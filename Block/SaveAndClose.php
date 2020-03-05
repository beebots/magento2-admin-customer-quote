<?php


namespace BeeBots\AdminCustomerQuote\Block;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Model\Session\Quote;
use Magento\Framework\Url\Helper\Data;
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

    /** @var Quote */
    private $quoteSession;

    /** @var Data */
    private $urlHelper;

    /**
     *
     * @param Quote $quoteSession
     * @param Data $urlHelper
     * @param Context $context
     * @param Create $createBlock
     * @param array $data
     */
    public function __construct(Quote $quoteSession, Data $urlHelper, Context $context, Create $createBlock, array $data = [])
    {
        parent::__construct($context, $data);
        $this->quoteSession = $quoteSession;
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
        $this->urlHelper = $urlHelper;
    }

    /**
     * Function: getCancelUrl
     *
     * @return string
     */
    public function getCancelUrl()
    {
        $cancelUrl = $this->createBlock->getCancelUrl();
        return $this->urlHelper->addRequestParam($cancelUrl, ['delete' => 'false']);
    }
}
