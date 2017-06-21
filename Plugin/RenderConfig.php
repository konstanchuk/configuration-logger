<?php

/**
 * Configuration Logger Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\ConfigurationLogger\Plugin;


class RenderConfig
{
    public function aroundRender(
        \Magento\Config\Block\System\Config\Form\Field $subject,
        \Closure $proceed,
        \Magento\Framework\Data\Form\Element\AbstractElement $element
    )
    {
        $result = $proceed($element);
        $iconAttributes = $this->getIconAttributes($element);
        if ($iconAttributes) {
            // <tr id="row_*"></tr>
            $search = 'id="row_' . $element->getHtmlId() . '"';
            $index = strpos($result, $search);
            if ($index !== false) {
                return substr_replace($result, $search . ' ' . $iconAttributes, $index, strlen($search));
            }
        }
        return $result;
    }

    protected function getIconAttributes(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $data = $element->getData();
        $originalData = $element->getOriginalData();
        if (isset($data['type']) && $data['type'] != 'file' && $originalData) {
            $options = [
                'scope' => isset($data['scope']) ? $data['scope'] : null,
                'scope_id' => isset($data['scope_id']) ? $data['scope_id'] : null,
                'path' => isset($originalData['path']) ? $originalData['path'] : null,
                'type' => $data['type'],
                'id' => isset($originalData['id']) ? $originalData['id'] : null,
                'label' => isset($data['label']) ? $data['label'] : null,
            ];
            $htmlOptions = [];
            foreach ($options as $key => $value) {
                if ($value) {
                    $htmlOptions[] = sprintf('data-%s="%s"', /* @noEscape */
                        $key, htmlspecialchars($value));
                }
            }
            return implode(' ', $htmlOptions);
        }
        return '';
    }
}