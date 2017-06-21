<?php

/**
 * Configuration Logger Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\ConfigurationLogger\Controller\Adminhtml\Restore;

use Konstanchuk\ConfigurationLogger\Model\ConfigLog;
use Konstanchuk\ConfigurationLogger\Api\ConfigLogRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
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


    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ConfigLogRepositoryInterface $configLogRepository
    )
    {
        parent::__construct($context);
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_configLogRepository = $configLogRepository;
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
        $logId = $request->getParam('log_id');
        try {
            /** @var ConfigLog $configLog */
            $configLog = $this->_configLogRepository->get($logId);
            $status = $this->_configLogRepository->restore($configLog);
        } catch (\Exception $e) {
            $status = false;
        }
        $result = $this->_resultJsonFactory->create();
        $result->setData(['status' => $status ? 'success' : 'error']);
        return $result;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Config::config');
    }
}