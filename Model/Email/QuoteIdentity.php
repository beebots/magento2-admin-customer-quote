<?php


namespace BeeBots\AdminCustomerQuote\Model\Email;


use Magento\Sales\Model\Order\Email\Container\Container;
use Magento\Sales\Model\Order\Email\Container\IdentityInterface;

/**
 * Class QuoteIdentity
 *
 * @package BeeBots\AdminCustomerQuote\Model\Email
 */
class QuoteIdentity extends Container implements IdentityInterface
{
    /**
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * @return array|bool
     */
    public function getEmailCopyTo()
    {
        return false;
    }

    /**
     * @return mixed
     */
    public function getCopyMethod()
    {
        return false;
    }

    /**
     * @return mixed
     */
    public function getGuestTemplateId()
    {
        // TODO: Implement getGuestTemplateId() method.
    }

    /**
     * @return mixed
     */
    public function getTemplateId()
    {
        // TODO: Implement getTemplateId() method.
    }

    /**
     * @return mixed
     */
    public function getEmailIdentity()
    {
        // TODO: Implement getEmailIdentity() method.
    }
}
