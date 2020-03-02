<?php

namespace BeeBots\AdminCustomerQuote\Controller\Adminhtml\Email;

use BeeBots\AdminCustomerQuote\Model\QuoteSender;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Quote\Model\QuoteRepository;
use Psr\Log\LoggerInterface;
use Throwable;

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

    /** @var LoggerInterface */
    private $logger;

    /**
     * Index constructor.
     *
     * @param RawFactory $resultRawFactory
     * @param QuoteRepository $quoteRepository
     * @param QuoteSender $quoteSender
     * @param Context $context
     * @param LoggerInterface $logger
     */
    public function __construct(
        RawFactory $resultRawFactory,
        QuoteRepository $quoteRepository,
        QuoteSender $quoteSender,
        Context $context,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->quoteRepository = $quoteRepository;
        $this->quoteSender = $quoteSender;
        $this->logger = $logger;
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
        try {
            $request = $this->getRequest();
            // get quote
            $quote = $this->quoteRepository->get($request->getParam('quote_id'));
            $this->quoteSender->send($quote);

            // send quote
            return $this->resultRawFactory->create()->setContents('');
        } catch (Throwable $t) {
            $this->logger->error($t->getMessage(), ['exception' => $t]);
            return $this->resultRawFactory->create()->setHttpResponseCode(500)->setContents('');
        }
    }
}
