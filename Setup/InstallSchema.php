<?php
/**
 * @author    Arkadij Kuzhel <akuzhel@gmail.com>
 * @created   10.04.18
 */

namespace MadePeople\MultiFactor\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 *
 */
class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $table = $setup->getConnection()
                       ->newTable($setup->getTable('multifactor_log'))
                       ->addColumn(
                           'id',
                           Table::TYPE_INTEGER,
                           null,
                           ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                           'Id'
                       )
                       ->addColumn(
                           'order_id',
                           Table::TYPE_INTEGER,
                           null,
                           [
                               'unsigned' => true,
                               'nullable' => false,
                           ],
                           'Order ID link'
                       )
                       ->addColumn(
                           'store_id',
                           Table::TYPE_SMALLINT,
                           null,
                           ['unsigned' => true],
                           'Store ID link'
                       )
                       ->addColumn(
                           'multi_factor',
                           Table::TYPE_DECIMAL,
                           '12,4',
                           [],
                           'Multiplication Factor'
                       )
                       ->addColumn(
                           'base_multi_total',
                           Table::TYPE_DECIMAL,
                           '12,4',
                           [],
                           'Base Multiplied Total'
                       )
                       ->addColumn(
                           'multi_total',
                           Table::TYPE_DECIMAL,
                           '12,4',
                           [],
                           'Multiplied Total'
                       )
                       ->addForeignKey(
                           $setup->getFkName('multifactor_log', 'order_id', 'sales_order', 'entity_id'),
                           'order_id',
                           $setup->getTable('sales_order'),
                           'entity_id',
                           Table::ACTION_CASCADE
                       )
                       ->addIndex(
                           $setup->getIdxName('multifactor_log', 'order_id', AdapterInterface::INDEX_TYPE_UNIQUE),
                           'order_id',
                           ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                       )
                       ->setComment('MultiFactor Log for Orders');
        $setup->getConnection()->createTable($table);
    }
}
