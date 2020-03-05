<?php


namespace BeeBots\AdminCustomerQuote\Block;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Model\Session\Quote;

/**
 * Class EmailQuote
 *
 * @package BeeBots\AdminCustomerQuote\Block
 */
class EmailQuote extends Template
{
    /** @var ButtonList */
    private $buttonList;

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
        $this->buttonList = $context->getButtonList();
        $this->buttonList->add(
            'email_quote',
            [
                'label' => __('Email Quote'),
                'id' => 'email_quote',
            ],
            1,
            0,
            'toolbar'
        );
    }

    /**
     * Function: getUrl
     *
     * @return mixed|string
     */
    public function getEmailQuoteUrl()
    {
        return $this->getUrl('adminquote/email');
    }
}
