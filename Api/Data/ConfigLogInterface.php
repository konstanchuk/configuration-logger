<?php

/**
 * Configuration Logger Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\ConfigurationLogger\Api\Data;


interface ConfigLogInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getScope();

    /**
     * @param string $scope
     * @return $this
     */
    public function setScope($scope);

    /**
     * @return string
     */
    public function getScopeId();

    /**
     * @param string $scopeId
     * @return $this
     */
    public function setScopeId($scopeId);

    /**
     * @return string
     */
    public function getPath();

    /**
     * @param string $path
     * @return $this
     */
    public function setPath($path);

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value);

    /**
     * @return int
     */
    public function getValueType();

    /**
     * @return int
     */
    public function getUserIp();

    /**
     * @param int $userIp
     * @return $this
     */
    public function setUserIp($userIp);

    /**
     * @return int
     */
    public function getUserId();

    /**
     * @param string $userId
     * @return $this
     */
    public function setUserId($userId);

    /**
     * @return int
     */
    public function getAction();

    /**
     * @param int $action
     * @return $this
     */
    public function setAction($action);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);
}