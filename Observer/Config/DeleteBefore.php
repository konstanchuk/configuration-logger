<?php

/**
 * Configuration Logger Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\ConfigurationLogger\Observer\Config;

use Konstanchuk\ConfigurationLogger\Observer\AbstractConfigObserver;
use Konstanchuk\ConfigurationLogger\Model\ConfigLog;
use Magento\Framework\Event\Observer;


class DeleteBefore extends AbstractConfigObserver
{
    public function execute(Observer $observer)
    {
        try {
            /** @var \Magento\Framework\App\Config\Value $configData */
            $configData = $observer->getData('config_data');
            if ($configData && $configData->getId()) {
                $this->_configLogRepository->dumpConfig($configData, ConfigLog::ACTION_DELETE);
            }
        } catch (\Exception $e) {
            $this->_logger->error(__('Config Logger error: $1', $e->getMessage()));
        }
    }
}