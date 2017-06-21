<?php

/**
 * Configuration Logger Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\ConfigurationLogger\Model\ResourceModel\ConfigLog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;


class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Konstanchuk\ConfigurationLogger\Model\ConfigLog',
            'Konstanchuk\ConfigurationLogger\Model\ResourceModel\ConfigLog'
        );
    }
}