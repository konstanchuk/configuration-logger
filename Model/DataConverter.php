<?php

/**
 * Configuration Logger Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\ConfigurationLogger\Model;

use Magento\User\Model\UserFactory;
use Magento\Framework\Api\SearchResults;
use Magento\Framework\Event\Manager as EventManager;


class DataConverter
{
    /** @var UserFactory */
    protected $userFactory;

    /** @var array */
    protected $customers = [];

    public function __construct(UserFactory $userFactory, EventManager $eventManager)
    {
        $this->userFactory = $userFactory;
    }

    public function prepareValues(SearchResults $values)
    {
        $result = [];
        $actions = [
            ConfigLog::ACTION_UPDATE => __('update'),
            ConfigLog::ACTION_CREATE => __('create'),
            ConfigLog::ACTION_DELETE => __('delete'),
            ConfigLog::ACTION_RESTORE => __('restore'),
            ConfigLog::ACTION_SAVE_PREVIOUS => __('previous'),
        ];
        foreach ($values->getItems() as $item) {
            /** @var ConfigLog $item */
            $result[] = [
                'log_id' => $item->getId(),
                'author' => $this->getAuthorName($item),
                'ip_address' => $item->getUserIp(),
                'value' => $item->getValueType() == ConfigLog::VALUE_TYPE_OBJECT ? false : (string)$item->getValue(),
                'is_normal_value' => $item->getValueType() == ConfigLog::VALUE_TYPE_STRING,
                'created' => $item->getCreatedAt(),
                'action' => isset($actions[$item->getAction()]) ? $actions[$item->getAction()] : $actions[ConfigLog::ACTION_UPDATE],
                'can_restore' => $item->canRestore(),
            ];
        }
        return $result;
    }

    protected function getAuthorName(ConfigLog $model)
    {
        $userId = $model->getUserId();
        if (!$userId) {
            return null;
        }
        if (!array_key_exists($userId, $this->customers)) {
            /** @var \Magento\User\Model\User $user */
            $user = $this->userFactory->create()->load($userId);
            if ($user->getId()) {
                $this->customers[$userId] = $user;
            } else {
                $this->customers[$userId] = null;
            }
        }
        $user = $this->customers[$userId];
        if (is_null($user)) {
            return null;
        } else {
            return $user->getFirstName() . ' ' . $user->getLastName();
        }
    }
}