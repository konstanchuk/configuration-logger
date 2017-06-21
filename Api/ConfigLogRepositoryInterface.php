<?php

/**
 * Configuration Logger Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\ConfigurationLogger\Api;


use Konstanchuk\ConfigurationLogger\Model\ConfigLog;
use Magento\Framework\App\Config\ScopeConfigInterface;

interface ConfigLogRepositoryInterface
{
    /**
     * @param \Konstanchuk\ConfigurationLogger\Api\Data\ConfigLogInterface $model
     * @return int
     */
    public function save(\Konstanchuk\ConfigurationLogger\Api\Data\ConfigLogInterface $model);

    /**
     * @param $id
     * @return \Konstanchuk\ConfigurationLogger\Api\Data\ConfigLogInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Konstanchuk\ConfigurationLogger\Api\Data\ConfigLogSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param \Konstanchuk\ConfigurationLogger\Api\Data\ConfigLogInterface $model
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Konstanchuk\ConfigurationLogger\Api\Data\ConfigLogInterface $model);

    /**
     * Delete model by ID.
     *
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);

    public function dumpConfig(\Magento\Framework\DataObject $config, $action = null);

    public function getValues($path, $scopeCode, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT);

    public function restore(ConfigLog $config);
}