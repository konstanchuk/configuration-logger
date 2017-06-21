<?php

/**
 * Configuration Logger Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\ConfigurationLogger\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;


interface ConfigLogSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Konstanchuk\ConfigurationLogger\Api\Data\ConfigLogInterface[]
     */
    public function getItems();

    /**
     * @param \Konstanchuk\ConfigurationLogger\Api\Data\ConfigLogInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}