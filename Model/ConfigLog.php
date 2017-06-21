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
use Magento\Framework\Model\AbstractModel;


class ConfigLog extends AbstractModel implements ConfigLogInterface
{
    const ACTION_CREATE = 1;
    const ACTION_UPDATE = 2;
    const ACTION_DELETE = 3;
    const ACTION_RESTORE = 4;
    const ACTION_SAVE_PREVIOUS = 5;

    const VALUE_TYPE_STRING = 0;
    const VALUE_TYPE_OBJECT = 1;

    /** @var \Magento\Framework\Stdlib\DateTime\DateTime */
    protected $_date;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_date = $date;
    }

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Konstanchuk\ConfigurationLogger\Model\ResourceModel\ConfigLog');
    }

    /**
     * @return string
     */
    public function getScope()
    {
        return $this->_getData('scope');
    }

    /**
     * @param string $scope
     * @return $this
     */
    public function setScope($scope)
    {
        return $this->setData('scope', $scope);
    }

    /**
     * @return string
     */
    public function getScopeId()
    {
        return $this->_getData('scope_id');
    }

    /**
     * @param string $scopeId
     * @return $this
     */
    public function setScopeId($scopeId)
    {
        return $this->setData('scope_id', $scopeId);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->_getData('path');
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        return $this->setData('path', $path);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        $value = $this->_getData('value');
        if ($this->getValueType() == static::VALUE_TYPE_STRING) {
            return $value;
        }
        return @unserialize($value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        if (is_scalar($value) || is_null($value)) {
            $this->setValueType(static::VALUE_TYPE_STRING);
        } else {
            if (is_array($value) || is_object($value)) {
                $value = serialize($value);
            } else {
                $value = null;
            }
            $this->setValueType(static::VALUE_TYPE_OBJECT);
        }
        return $this->setData('value', $value);
    }

    /**
     * @return int
     */
    public function getValueType()
    {
        return $this->_getData('value_type');
    }

    /**
     * @param int $type
     * @return $this
     */
    protected function setValueType($type)
    {
        return $this->setData('value_type', $type);
    }

    /**
     * @return int
     */
    public function getUserIp()
    {
        return $this->_getData('user_ip');
    }

    /**
     * @param int $userIp
     * @return $this
     */
    public function setUserIp($userIp)
    {
        return $this->setData('user_ip', $userIp);
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->_getData('user_id');
    }

    /**
     * @param string $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        return $this->setData('user_id', $userId);
    }

    /**
     * @return int
     */
    public function getAction()
    {
        return $this->_getData('action');
    }

    /**
     * @param int $action
     * @return $this
     */
    public function setAction($action)
    {
        return $this->setData('action', $action);
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->_getData('created_at');
    }

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData('created_at', $createdAt);
    }

    public function beforeSave()
    {
        if (!$this->getCreatedAt()) {
            $this->setCreatedAt($this->_date->gmtDate());
        }
        return parent::beforeSave();
    }

    public function canRestore()
    {
        return $this->getValueType() == static::VALUE_TYPE_STRING
            && $this->getAction() != static::ACTION_RESTORE
            && $this->getAction() != static::ACTION_DELETE;
    }
}