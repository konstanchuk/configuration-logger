<?php

/**
 * Configuration Logger Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\ConfigurationLogger\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;


class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $tableName = $installer->getTable('konstanchuk_core_config_data_log');
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Id'
                )
                ->addColumn(
                    'scope',
                    Table::TYPE_TEXT,
                    8,
                    ['nullable' => false, 'default' => 'default'],
                    'Config Scope'
                )
                ->addColumn(
                    'scope_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => '0'],
                    'Config Scope Id'
                )
                ->addColumn(
                    'path',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => 'general'],
                    'Config Path'
                )
                ->addColumn(
                    'value',
                    Table::TYPE_TEXT,
                    '64k',
                    [],
                    'Config Value'
                )
                ->addColumn(
                    'value_type',
                    Table::TYPE_INTEGER,
                    1,
                    ['nullable' => false, 'default' => '0'],
                    'Value Type'
                )
                ->addColumn(
                    'user_ip',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'User Api'
                )
                ->addColumn(
                    'user_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => true],
                    'Admin User Id'
                )
                ->addColumn(
                    'action',
                    Table::TYPE_INTEGER,
                    1,
                    ['nullable' => false, 'default' => '1'],
                    'Config Scope Id'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'Created At'
                )
                ->addIndex(
                    $setup->getIdxName(
                        'core_config_data',
                        ['scope', 'scope_id', 'path'],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                    ),
                    ['scope', 'scope_id', 'path'],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX]
                )
                ->setComment('Config Data Logs')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');

            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}