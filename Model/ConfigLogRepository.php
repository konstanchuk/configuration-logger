<?php

/**
 * Configuration Logger Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\ConfigurationLogger\Model;

use Konstanchuk\ConfigurationLogger\Api\Data\ConfigLogInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\DataObjectHelper;

;
use Magento\Framework\Reflection\DataObjectProcessor;
use Konstanchuk\ConfigurationLogger\Api\ConfigLogRepositoryInterface;


class ConfigLogRepository implements ConfigLogRepositoryInterface
{
    /** @var \Magento\Framework\Stdlib\DateTime\DateTime */
    protected $_date;

    /** @var \Konstanchuk\ConfigurationLogger\Api\Data\ConfigLogInterface[] */
    protected $entities = [];

    /** @var bool */
    protected $allLoaded = false;

    /** @var \Konstanchuk\ConfigurationLogger\Model\ResourceModel\ConfigLog */
    protected $resource;

    /** @var \Konstanchuk\ConfigurationLogger\Model\ConfigLogFactory */
    protected $modelFactory;

    /** @var \Konstanchuk\ConfigurationLogger\Model\ResourceModel\ConfigLog\CollectionFactory */
    protected $collectionFactory;

    /** @var \Konstanchuk\ConfigurationLogger\Api\Data\ConfigLogSearchResultsInterfaceFactory */
    protected $searchResultsFactory;

    /** @var \Magento\Framework\Api\SearchCriteriaBuilderFactory */
    protected $searchCriteriaBuilderFactory;

    /** @var \Magento\Framework\Api\SortOrderBuilderFactory */
    protected $sortOrderBuilderFactory;

    /** @var DataObjectHelper */
    protected $dataObjectHelper;

    /** @var DataObjectProcessor */
    protected $dataObjectProcessor;

    /** @var \Magento\Framework\App\Config\Storage\WriterInterface */
    protected $_configWriter;

    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resourceConnection;

    /** @var ScopeConfigInterface */
    protected $_scopeConfig;

    public function __construct(
        \Konstanchuk\ConfigurationLogger\Model\ResourceModel\ConfigLog $resource,
        \Konstanchuk\ConfigurationLogger\Model\ConfigLogFactory $modelFactory,
        \Konstanchuk\ConfigurationLogger\Model\ResourceModel\ConfigLog\CollectionFactory $collectionFactory,
        \Konstanchuk\ConfigurationLogger\Api\Data\ConfigLogSearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        \Magento\Framework\Api\SortOrderBuilderFactory $sortOrderBuilderFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->resource = $resource;
        $this->modelFactory = $modelFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->sortOrderBuilderFactory = $sortOrderBuilderFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->_date = $date;
        $this->_configWriter = $configWriter;
        $this->_resourceConnection = $resourceConnection;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @param \Konstanchuk\ConfigurationLogger\Api\Data\ConfigLogInterface $model
     * @return int
     */
    public function save(\Konstanchuk\ConfigurationLogger\Api\Data\ConfigLogInterface $model)
    {
        try {
            $this->resource->save($model);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
        return $model->getId();
    }

    /**
     * @param $id
     * @return \Konstanchuk\ConfigurationLogger\Api\Data\ConfigLogInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id)
    {
        if (isset($this->entities[$id])) {
            return $this->entities[$id];
        }
        /* @var $model \Konstanchuk\ConfigurationLogger\Model\ConfigLog */
        $model = $this->modelFactory->create();
        $this->resource->load($model, $id);
        if (!$model->getId()) {
            throw new NoSuchEntityException(__('Model does not exist'));
        }
        $this->entities[$id] = $model;
        return $model;
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResults
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $collection = $this->collectionFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrdersData = $searchCriteria->getSortOrders();
        if ($sortOrdersData) {
            foreach ($sortOrdersData as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    $sortOrder->getDirection()
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $items = [];
        /** @var \Konstanchuk\ConfigurationLogger\Model\ConfigLog $item */
        foreach ($collection as $item) {
            /** @var \Konstanchuk\ConfigurationLogger\Model\ConfigLog $model */
            $model = $this->modelFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $model,
                $item->getData(),
                'Konstanchuk\ConfigurationLogger\Api\Data\ConfigLogInterface'
            );
            $items[] = $model;
        }
        $searchResults->setItems($items);
        return $searchResults;
    }

    /**
     * @param int $id
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function deleteById($id)
    {
        $model = $this->get($id);
        return $this->delete($model);
    }

    public function delete(\Konstanchuk\ConfigurationLogger\Api\Data\ConfigLogInterface $model)
    {
        try {
            $this->resource->delete($model);
            if (isset($this->entities[$model->getId()])) {
                unset($this->entities[$model->getId()]);
            }
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Unable to remove entity with id "%1"', $model->getId()),
                $exception
            );
        }
        return true;
    }

    public function dumpConfig(\Magento\Framework\DataObject $config, $action = null)
    {
        if (is_null($action)) {
            $action = ConfigLog::ACTION_CREATE;
        }
        /* @var $model \Konstanchuk\ConfigurationLogger\Model\ConfigLog */
        $model = $this->modelFactory->create();
        $model->setScope($config->getScope());
        $model->setScopeId($config->getScopeId());
        $model->setPath($config->getPath());
        $model->setValue($config->getValue());
        $model->setCreatedAt($this->_date->gmtDate());
        $model->setAction($action);
        $model->setUserIp(null);
        $model->setUserId(null);

        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $state = $om->get('Magento\Framework\App\State');
        if ('adminhtml' === $state->getAreaCode() && php_sapi_name() != 'cli') {
            /** @var \Magento\User\Model\User $adminUser */
            $adminUser = $om->get('Magento\Backend\Model\Auth\Session')->getUser();
            /** @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $removeAddress */
            $removeAddress = $om->get('Magento\Framework\HTTP\PhpEnvironment\RemoteAddress');
            if ($adminUser) {
                $model->setUserId($adminUser->getId());
                $model->setUserIp($removeAddress->getRemoteAddress());
            }
        }

        $previousModel = clone $model;
        $previousValue = $this->_scopeConfig->getValue($model->getPath(), $model->getScope(), $model->getScopeId());
        $previousModel->setValue($previousValue);
        $previousModel->setAction(ConfigLog::ACTION_SAVE_PREVIOUS);

        $connection = $this->_resourceConnection->getConnection();
        try {
            $connection->beginTransaction();
            $this->save($model);
            $this->save($previousModel);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
        return [$model, $previousModel];
    }

    public function getValues($path, $scopeId, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        /** @var \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder */
        $sortOrderBuilder = $this->sortOrderBuilderFactory->create();
        $sortOrders = $sortOrderBuilder->setField('created_at')->setDirection(SortOrder::SORT_DESC)->create();
        $searchCriteria = $searchCriteriaBuilder->addFilter('path', $path, 'eq')
            ->addFilter('scope_id', $scopeId, 'eq')
            ->addFilter('scope', $scopeType, 'eq')
            ->addSortOrder($sortOrders)
            ->create();
        return $this->getList($searchCriteria);
    }

    public function restore(ConfigLog $configLog)
    {
        if (!$configLog->canRestore()) {
            return false;
        }
        $connection = $this->_resourceConnection->getConnection();
        try {
            $connection->beginTransaction();
            $this->_configWriter->save($configLog->getPath(), $configLog->getValue(), $configLog->getScope(), $configLog->getScopeId());
            $this->dumpConfig($configLog, ConfigLog::ACTION_RESTORE);
            $connection->commit();
            return true;
        } catch (\Exception $e) {
            $connection->rollBack();
        }
        return false;
    }
}