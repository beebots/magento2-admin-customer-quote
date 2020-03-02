<?php


namespace BeeBots\AdminCustomerQuote\Model\QuoteSender;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Quote\Model\Quote;

/**
 * Class SendMail
 *
 * @package BeeBots\AdminCustomerQuote\Model\QuoteSender
 */
class SendMail
{
    /** @var TransportBuilder */
    private $transportBuilder;

    /**
     * SendMail constructor.
     *
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(TransportBuilder $transportBuilder)
    {
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * Function: sendEmail
     *
     * @param string $templateId
     * @param Quote $quote
     * @param array $templateVars
     * @param array $from
     * @param string $email
     * @param bool|string|array $cc
     *
     * @throws LocalizedException
     * @throws MailException
     */
    public function send(string $templateId, Quote $quote, array $templateVars, array $from, string $email, $cc = false)
    {
        $this->sendEmail($templateId, $quote, $templateVars, $from, $email, $cc);
    }

    /**
     * Function: sendEmail
     *
     * @param string $templateId
     * @param Quote $quote
     * @param array $templateVars
     * @param array $from
     * @param string $email
     * @param bool|string|array $cc
     *
     * @throws LocalizedException
     * @throws MailException
     */
    public function sendCopyTo(
        string $templateId,
        Quote $quote,
        array $templateVars,
        array $from,
        string $email,
        $cc = false
    ) {
        $this->sendEmail($templateId, $quote, $templateVars, $from, $email, $cc);
    }

    /**
     * Function: sendEmail
     *
     * @param string $templateId
     * @param Quote $quote
     * @param array $templateVars
     * @param array $from
     * @param string $email
     * @param bool $cc
     *
     * @throws LocalizedException
     * @throws MailException
     */
    private function sendEmail(
        string $templateId,
        Quote $quote,
        array $templateVars,
        array $from,
        string $email,
        $cc = false
    ) {
        $transportBuilder = $this->transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions(['area' => 'frontend', 'store' => $quote->getStoreId()])
            ->setTemplateVars($templateVars)
            ->setFrom($from)
            ->addTo($email);

        if ($cc) {
            $transportBuilder->addCc($cc);
        }

        $transport = $transportBuilder->getTransport();

        $transport->sendMessage();
    }
}
