<?php

namespace BeeBots\AdminCustomerQuote\Model;

use DateTime;
use DateTimeZone;
use Magento\Customer\Model\Address\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Payment\Helper\Data;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Payment;
use Psr\Log\LoggerInterface;
use Throwable;

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

    /** @var Magento\Framework\Stdlib\DateTime\TimezoneInterface */
    private $timezone;

    /** @var Renderer */
    private $addressRenderer;

    /** @var Config */
    private $addressConfig;

    /** @var Data */
    private $paymentHelper;

    /**
     * QuoteSender constructor.
     *
     * @param LoggerInterface $logger
     * @param TransportBuilder $transportBuilder
     * @param ScopeConfigInterface $scopeConfig
     * @param SenderResolverInterface $senderResolver
     * @param TimezoneInterface $timezone
     * @param Config $addressConfig
     * @param Data $paymentHelper
     */
    public function __construct(
        LoggerInterface $logger,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        SenderResolverInterface $senderResolver,
        TimezoneInterface $timezone,
        Config $addressConfig,
        Data $paymentHelper
    ) {
        $this->logger = $logger;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->senderResolver = $senderResolver;
        $this->timezone = $timezone;
        $this->addressConfig = $addressConfig;
        $this->paymentHelper = $paymentHelper;
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
            ->setTemplateVars(
                [
                    'quote' => $quote,
                    'quote_updated_at' => $this->getFormattedDateFromDateTimeString($quote->getUpdatedAt()),
                    'quote_comment' => $this->getCustomerNote($quote),
                    'quote_show_shipping_address' => ! $quote->getIsVirtual(),
                    'quote_shipping_address' => $this->getFormattedAddress($quote->getShippingAddress(), 'html'),
                    'quote_billing_address' => $this->getFormattedAddress($quote->getBillingAddress(), 'html'),
                    'quote_is_not_virtual' => ! $quote->getIsVirtual(),
                    'quote_shipping_method' => $quote->getShippingAddress()->getShippingMethod(),
                    'quote_shipping_description' => $quote->getShippingAddress()->getShippingDescription(),
                    'quote_payment_html' => $this->getPaymentHtml($quote),
                ]
            )
            ->setFrom($from)
            ->addTo($email)
            ->getTransport();

        $transport->sendMessage();
    }

    /**
     * Function: getFormattedDateFromDateTimeString
     *
     * @param $date
     *
     * @return string
     */
    private function getFormattedDateFromDateTimeString($date)
    {
        $timeZone = new DateTimeZone($this->timezone->getConfigTimezone());
        return DateTime::createFromFormat('Y-m-d H:i:s', $date)->setTimezone($timeZone)->format('M jS, Y g:i:sa T');
    }

    /**
     * Function: getCustomerNote
     *
     * @param Quote $quote
     *
     * @return mixed|string|null
     */
    private function getCustomerNote(Quote $quote)
    {
        if ($quote->getCustomerNoteNotify()) {
            return $quote->getCustomerNote();
        }
        return '';
    }

    /**
     * Function: getFormattedAddress
     *
     * @param Address $address
     * @param string $type
     *
     * @return string|null
     */
    private function getFormattedAddress(Address $address, $type)
    {
        $formatType = $this->addressConfig->getFormatByCode($type);
        // make sure there is a value here so the template doesn't blow up
        // template will blow up if there are no values for any address
        // make sure at least a space is there for first name
        if (! $address->getFirstname()) {
            $address->setFirstname(' ');
        }
        return $formatType->getRenderer()->renderArray($address->getData());
    }

    /**
     * Get payment info block as html
     *
     * @param Quote $quote
     *
     * @return string
     * @throws \Exception
     */
    private function getPaymentHtml(Quote $quote)
    {
        try {
            return $this->paymentHelper->getInfoBlockHtml(
                $quote->getPayment(),
                $quote->getStoreId()
            );
        } catch (Throwable $t) {
            if ($t->getMessage() !== 'The payment method you requested is not available.') {
                $this->logger->error($t->getMessage(), ['exception' => $t]);
            }
        }
        return '';
    }
}
