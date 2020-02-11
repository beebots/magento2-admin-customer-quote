<?php

namespace BeeBots\AdminCustomerQuote\Plugin;

use Magento\Backend\Model\Session\Quote;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Quote\Model\QuoteRepository;
use Magento\Sales\Controller\Adminhtml\Order\Create\Cancel;

/**
 * Class DeleteCanceledQuote
 *
 * @package BeeBots\AdminCustomerQuote\Plugin
 */
class DeleteCanceledQuote
{
    /** @var RequestInterface|Http */
    private $request;

    /** @var QuoteRepository */
    private $quoteRepository;

    /** @var Quote */
    private $quoteSession;

    /**
     * DeleteCanceledQuote constructor.
     *
     * @param Http|RequestInterface $request
     * @param QuoteRepository $quoteRepository
     * @param Quote $quoteSession
     */
    public function __construct(RequestInterface $request, QuoteRepository $quoteRepository, Quote $quoteSession)
    {
        $this->request = $request;
        $this->quoteRepository = $quoteRepository;
        $this->quoteSession = $quoteSession;
    }

    /**
     * Function: aroundExecute
     *
     * @param Cancel $subject
     * @param callable $proceed
     *
     * @return mixed
     */
    public function aroundExecute(Cancel $subject, callable $proceed)
    {
        $shouldDelete = $this->request->getParam('delete', true);
        if ($shouldDelete === 'false') {
            return $proceed();
        }
        $quote = $this->quoteSession->getQuote();
        $result = $proceed();
        // quote may never be active, but is a precautionary check. active quotes belong to customer, so we don't want to delete those
        if ($quote->getIsActive() === false) {
            $this->quoteRepository->delete($quote);
        }
        return $result;
    }
}
