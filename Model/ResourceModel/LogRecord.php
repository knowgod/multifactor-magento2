<?php
/**
 * @author    Arkadij Kuzhel <akuzhel@gmail.com>
 * @created   10.04.18
 */

namespace MadePeople\MultiFactor\Model\ResourceModel;

use MadePeople\MultiFactor\Api\Data\LogRecordInterface;

/**
 * Class LogRecord
 *
 */
class LogRecord extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
       $this->_init(LogRecordInterface::MAIN_TABLE, LogRecordInterface::ID);
    }
}
