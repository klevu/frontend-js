<?php

namespace Klevu\FrontendJs\Block;

use Klevu\FrontendJs\Api\IsEnabledConditionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Template as CoreTemplate;

class Template extends CoreTemplate
{
    /**
     * @var IsEnabledConditionInterface[]
     */
    private $isEnabledConditions;

    /**
     * @return IsEnabledConditionInterface[]
     */
    private function getIsEnabledConditions()
    {
        if (null === $this->isEnabledConditions) {
            $this->isEnabledConditions = [];
            $isEnabledConditionsFqcn = $this->getData('is_enabled_conditions_fqcn');
            if (is_array($isEnabledConditionsFqcn)) {
                $objectManager = ObjectManager::getInstance();
                foreach ($isEnabledConditionsFqcn as $key => $isEnabledConditionFqcn) {
                    $isEnabledCondition = $objectManager->get($isEnabledConditionFqcn);

                    if (!($isEnabledCondition instanceof IsEnabledConditionInterface)) {
                        $this->_logger->warning(sprintf(
                            'IsEnabledCondition "%s" must be instance of %s; %s received',
                            $key,
                            IsEnabledConditionInterface::class,
                            is_object($isEnabledCondition) ? get_class($isEnabledCondition) : gettype($isEnabledCondition)
                        ));
                        continue;
                    }

                    $this->isEnabledConditions[$key] = $isEnabledCondition;
                }
            }
        }

        return $this->isEnabledConditions;
    }

    /**
     * @return bool
     */
    private function isEnabled()
    {
        $isEnabledConditions = $this->getIsEnabledConditions();
        if (!$isEnabledConditions) {
            return true;
        }

        try {
            $store = $this->_storeManager->getStore();
            $storeId = (int)$store->getId();
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());

            return false;
        }

        $return = false;
        foreach ($isEnabledConditions as $isEnabledCondition) {
            if ($isEnabledCondition->execute($storeId)) {
                $return = true;
                break;
            }
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->isEnabled()) {
            return '';
        }

        return parent::_toHtml();
    }
}
