<?php


namespace BeeBots\AdminCustomerQuote\Controller\Adminhtml\Email;

use BeeBots\AdminCustomerQuote\Model\QuoteSender;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Quote\Model\QuoteRepository;

/**
 * Class Index
 *
 * @package BeeBots\AdminCustomerQuote\Controller\Adminhtml\Email
 */
class Index extends Action
{
    /** @var RawFactory */
    private $resultRawFactory;

    /** @var QuoteRepository */
    private $quoteRepository;

    /** @var QuoteSender */
    private $quoteSender;

    /**
     * Index constructor.
     *
     * @param RawFactory $resultRawFactory
     * @param QuoteRepository $quoteRepository
     * @param QuoteSender $quoteSender
     * @param Context $context
     */
    public function __construct(
        RawFactory $resultRawFactory,
        QuoteRepository $quoteRepository,
        QuoteSender $quoteSender,
        Context $context
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->quoteRepository = $quoteRepository;
        $this->quoteSender = $quoteSender;
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $request = $this->getRequest();
        // get quote
        $quote = $this->quoteRepository->get($request->getParam('quote_id'));
        $this->quoteSender->send($quote);

        // send quote
        return $this->resultRawFactory->create()->setsetContents('');
    }
}
