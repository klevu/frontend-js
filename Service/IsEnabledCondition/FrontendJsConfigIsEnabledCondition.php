<?php

namespace Klevu\FrontendJs\Service\IsEnabledCondition;

use Klevu\FrontendJs\Api\IsEnabledConditionInterface;
use Klevu\FrontendJs\Constants;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class FrontendJsConfigIsEnabledCondition implements IsEnabledConditionInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * IsEnabledCondition constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function execute($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            Constants::XML_PATH_FRONTENDJS_ENABLED,
            ScopeInterface::SCOPE_STORES,
            $storeId
        );
    }
}
