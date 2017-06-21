<?php

/**
 * Configuration Logger Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\ConfigurationLogger\Observer;

use Magento\Framework\Event\ObserverInterface;
use Konstanchuk\ConfigurationLogger\Api\ConfigLogRepositoryInterface;
use Psr\Log\LoggerInterface;


abstract class AbstractConfigObserver implements ObserverInterface
{
    /** @var ConfigLogRepositoryInterface */
    protected $_configLogRepository;

    /** @var LoggerInterface */
    protected $_logger;

    public function __construct(
        ConfigLogRepositoryInterface $configLogRepository,
        LoggerInterface $logger
    )
    {
        $this->_configLogRepository = $configLogRepository;
        $this->_logger = $logger;
    }
}