<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Sales Order Email order items
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace BeeBots\AdminCustomerQuote\Block\Quote\Email;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Block\Items\AbstractItems;
use Psr\Log\LoggerInterface;

/**
 * Sales Order Email items.
 *
 * @api
 * @since 100.0.2
 */
class Items extends AbstractItems
{
    /**
     * @var CartRepositoryInterface|mixed|null
     */
    private ?CartRepositoryInterface $cartRepository;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Items Constructor

     * @param Context $context
     * @param LoggerInterface $logger
     * @param CartRepositoryInterface|null $cartRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        ?CartRepositoryInterface $cartRepository = null,
        array $data = [],
    ) {
        $this->cartRepository = $cartRepository ?: ObjectManager::getInstance()->get(CartRepositoryInterface::class);
        parent::__construct($context, $data);
        $this->logger = $logger;
    }

    /**
     * Function: getQuote
     *
     * @return CartInterface|null
     */
    public function getQuote(): ?CartInterface
    {
        $quoteId = (int)$this->getData('quote_id');
        if (!$quoteId) {
            return null;
        }

        try {
            $quote = $this->cartRepository->get($quoteId);
            // THis needs to be 'order' so that the item child blocks work correctly without modification
            $this->setData('order', $quote);
            return $this->getData('order');
        } catch (NoSuchEntityException $e) {
            $this->logger->error("Error retrieving quote id: $quoteId", ['exception' => $e]);
        }

        return null;
    }
}
