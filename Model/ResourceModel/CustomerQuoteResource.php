<?php


namespace BeeBots\AdminCustomerQuote\Model\ResourceModel;


use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

/**
 * Class CustomerQuoteResource
 *
 * @package BeeBots\AdminCustomerQuote\Model\ResourceModel
 */
class CustomerQuoteResource extends AbstractDb
{
    /**
     * Initialize table nad PK name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('quote', 'entity_id');
    }

    /**
     * Function: getLatestQuoteIdOrNullForCustomer
     *
     * @param int $id
     *
     * @return int|null
     */
    public function getLatestQuoteIdOrNullForCustomer(int $id): ?int
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable('quote'), 'entity_id')
            ->where('customer_id = ?', $id)
            ->where('is_active = ?', 0)
            ->where('reserved_order_id is null')
            ->order(
                'updated_at ' . Select::SQL_DESC
            )->limit(
                1
            );

        $data = $connection->fetchRow($select);

        if ($data) {
            return $data['entity_id'];
        }
        return null;
    }
}
