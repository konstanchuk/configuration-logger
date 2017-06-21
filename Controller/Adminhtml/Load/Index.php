<?php

/**
 * Configuration Logger Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\ConfigurationLogger\Controller\Adminhtml\Load;

use Konstanchuk\ConfigurationLogger\Api\ConfigLogRepositoryInterface;
use Konstanchuk\ConfigurationLogger\Model\DataConverter;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResponseInterface;


class Index extends Action
{
    /**
     * Json Result page factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /** @var  ConfigLogRepositoryInterface */
    protected $_configLogRepository;

    /** @var  DataConverter */
    protected $_dataConverter;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ConfigLogRepositoryInterface $configLogRepository,
        DataConverter $dataConverter
    )
    {
        parent::__construct($context);
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_configLogRepository = $configLogRepository;
        $this->_dataConverter = $dataConverter;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $request = $this->getRequest();
        $scope = $request->getParam('scope', ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $scopeId = $request->getParam('scope_id', 0);
        $path = $request->getParam('path');
        $optionId = $request->getParam('id');
        $data = [
            'key' => [
                'scope' => $scope,
                'scopeId' => $scopeId,
                'path' => $path,
                'id' => $optionId
            ],
            'data' => [],
        ];
        if ($path && $optionId) {
            $path = $path . '/' . $optionId;
            try {
                $values = $this->_configLogRepository->getValues($path, $scopeId, $scope);
                $values = $this->_dataConverter->prepareValues($values);
                $data['data'] = $values;
            } catch (\Exception $e) {
                $data['error'] = $e->getMessage();
            }
        }
        $result = $this->_resultJsonFactory->create();
        $result->setData($data);
        return $result;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Config::config');
    }
}