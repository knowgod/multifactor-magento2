<?php
/**
 * @author    Arkadij Kuzhel <akuzhel@gmail.com>
 * @created   10.04.18
 */

namespace MadePeople\MultiFactor\Api\Data;

use Magento\Sales\Model\Order;

/**
 * Interface LogRecordInterface
 */
interface LogRecordInterface
{
    /**#@+
     * Model fields
     */
    const ID               = 'id';
    const ORDER_ID         = 'order_id';
    const STORE_ID         = 'store_id';
    const MULTI_FACTOR     = 'multi_factor';
    const BASE_MULTI_TOTAL = 'base_multi_total';
    const MULTI_TOTAL      = 'multi_total';
    /**#@-*/

    const MAIN_TABLE = 'multifactor_log';

    /**
     * @param int $storeId
     *
     * @return bool
     */
    public function isEnabled($storeId);

    /**
     * @param Order $order
     *
     * @return LogRecordInterface
     */
    public function processOrder(Order $order);

    /**
     * @return int|null
     */
    public function getOrderId();

    /**
     * @return int|null
     */
    public function getStoreId();
}
