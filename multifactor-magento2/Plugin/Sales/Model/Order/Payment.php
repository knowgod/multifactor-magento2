<?php
/**
 * @author    Arkadij Kuzhel <akuzhel@gmail.com>
 * @created   10.04.18
 */

namespace MadePeople\MultiFactor\Plugin\Sales\Model\Order;

use MadePeople\MultiFactor\Api\Data\LogRecordInterfaceFactory as LogRecordFactory;
use Magento\Sales\Model\Order\Payment as OrderPayment;

/**
 * Class Payment
 *
 */
class Payment
{
    /**
     * @var LogRecordFactory
     */
    protected $logRecordFactory;

    public function __construct(LogRecordFactory $logRecordFactory)
    {
        $this->logRecordFactory = $logRecordFactory;
    }

    /**
     * @see \Magento\Sales\Model\Order\Payment::pay
     *
     * @param OrderPayment $payment
     * @param OrderPayment $result
     *
     * @return mixed
     */
    function afterPay(OrderPayment $payment, $result)
    {
        $this->saveLogRecord($payment);

        return $result;
    }

    /**
     * @param OrderPayment $payment
     *
     * @return void
     */
    protected function saveLogRecord(OrderPayment $payment)
    {
        $order = $payment->getOrder();
        if (!($order instanceof \Magento\Sales\Model\Order) || 0 != $order->getBaseTotalDue()) {
            return;
        }

        /** @var \MadePeople\MultiFactor\Api\Data\LogRecordInterface $logRecord */
        $logRecord = $this->logRecordFactory->create();
        if ($logRecord->isEnabled($order->getStore()->getId())) {
            $logRecord->processOrder($order);
        }
    }
}
