<?php
/**
 * s@author    Arkadij Kuzhel <akuzhel@gmail.com>
 * @created   10.04.18
 */

namespace MadePeople\MultiFactor\Api\Data;

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
     * @param \Magento\Sales\Model\Order $order
     *
     * @return LogRecordInterface
     */
    public function processOrder(\Magento\Sales\Model\Order $order);

    /**
     * @return int|null
     */
    public function getOrderId();

    /**
     * @return int|null
     */
    public function getStoreId();
}
