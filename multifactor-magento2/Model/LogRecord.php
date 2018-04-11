<?php
/**
 * @author    Arkadij Kuzhel <akuzhel@gmail.com>
 * @created   10.04.18
 */

namespace MadePeople\MultiFactor\Model;

use MadePeople\MultiFactor\Api\Data\LogRecordInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Record
 *
 */
class LogRecord extends AbstractModel implements LogRecordInterface
{
    /**
     * Storage precision for decimal numbers
     */
    const PRECISION = 4;

    const XML_PATH_FACTOR_VALUE     = 'multifactor/general/value';
    const XML_PATH_FACTOR_IS_ACTIVE = 'multifactor/general/is_active';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * LogRecord constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * {@inheritdoc}
     *
     * @throws LocalizedException
     */
    public function __construct(
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @throws LocalizedException
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\LogRecord::class);
    }

    /**
     * @param int $storeId
     *
     * @return bool
     */
    public function isEnabled($storeId)
    {
        $status = $this->scopeConfig->getValue(
            self::XML_PATH_FACTOR_IS_ACTIVE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return (bool) $status ?? false;
    }

    /**
     * @param Order $order
     *
     * @return LogRecordInterface
     */
    public function processOrder(Order $order)
    {
        try {
            $orderId = $order->getId();
            if (!$orderId) {
                throw new LocalizedException(__('Order ID is not defined for payment'));
            }

            $this->getResource()->load($this, $orderId, self::ORDER_ID);
            if ($this->getOrderId()) {
                return $this;
            }

            $this->setStoreId($order->getStore()->getId());

            $recordData = [
                self::ORDER_ID         => $orderId,
                self::MULTI_FACTOR     => $this->getMultiFactor(),
                self::BASE_MULTI_TOTAL => $this->multiplyByFactor($order->getBaseGrandTotal()),
                self::MULTI_TOTAL      => $this->multiplyByFactor($order->getGrandTotal()),
            ];
            $this->setData($recordData);

            try {
                $this->getResource()->save($this);
            } catch (\Exception $e) {
                throw new LocalizedException(__('Log not Recorded (%1).', $e->getMessage()));
            }

        } catch (LocalizedException $e) {
            $this->_logger->error(__('Unable to log MultiFactor: %1', $e->getMessage()));
        }

        return $this;
    }


    /**
     * @return int|null
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @return float
     */
    protected function getMultiFactor()
    {
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_FACTOR_VALUE,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );

        return floatval($value ?? 1);
    }

    /**
     * @param $number
     *
     * @return float|int
     */
    protected function multiplyByFactor($number)
    {
        $decade = pow(10, self::PRECISION);

        $factor = round($this->getMultiFactor() * $decade, self::PRECISION);
        $number = round($number * $decade, self::PRECISION);

        return ($factor * $number) / ($decade * $decade);
    }

    /**
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->_getData(self::STORE_ID);
    }

    /**
     * @param $storeId
     *
     * @return $this
     */
    protected function setStoreId($storeId)
    {
        $this->setData(self::STORE_ID, $storeId);

        return $this;
    }
}
