<?php


namespace BeeBots\AdminCustomerQuote\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Quote\Model\Quote;
use Psr\Log\LoggerInterface;

/**
 * Class QuoteNotifier
 *
 * @package BeeBots\AdminCustomerQuote\Model
 */
class QuoteSender
{
    const EMAIL_TEMPLATE_CONFIG_PATH = 'quote_email/quote/template';
    const EMAIL_SENDER_CONFIG_PATH = 'quote_email/quote/email_identity';

    /** @var TransportBuilder */
    private $transportBuilder;

    /** @var LoggerInterface */
    private $logger;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var SenderResolverInterface */
    private $senderResolver;

    /**
     * QuoteSender constructor.
     *
     * @param LoggerInterface $logger
     * @param TransportBuilder $transportBuilder
     * @param ScopeConfigInterface $scopeConfig
     * @param SenderResolverInterface $senderResolver
     */
    public function __construct(
        LoggerInterface $logger,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        SenderResolverInterface $senderResolver
    ) {
        $this->logger = $logger;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->senderResolver = $senderResolver;
    }

    /**
     * Function: send
     *
     * @param Quote $quote
     */
    public function send(Quote $quote)
    {
        $templateId = $this->scopeConfig->getValue(self::EMAIL_TEMPLATE_CONFIG_PATH);
        $email = $quote->getCustomerEmail();

        /** @var array $from */
        $from = $this->senderResolver->resolve(
            $this->scopeConfig->getValue(self::EMAIL_SENDER_CONFIG_PATH),
        );

        $transport = $this->transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions(['area' => 'frontend', 'store' => $quote->getStoreId()])
            ->setTemplateVars([])
            ->setFrom($from)
            ->addTo($email)
            ->getTransport();

        $transport->sendMessage();
    }
}
