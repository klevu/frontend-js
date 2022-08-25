<?php

namespace Klevu\FrontendJs\Block\Html\Head;

use Klevu\Search\Helper\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class JsPreConnect extends Template
{
    /**
     * @return string
     */
    public function getPreConnectUrl()
    {
        list($scope, $scopeId) = $this->getScope();
        $protocol = 'https://';

        return $protocol .
            $this->_scopeConfig->getValue(
                Config::XML_PATH_JS_URL,
                $scope,
                $scopeId
            );
    }

    /**
     * @return array
     */
    private function getScope()
    {
        try {
            $store = $this->_storeManager->getStore();
            $scope = ScopeInterface::SCOPE_STORES;
            $scopeId = $store->getId();
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
            $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
            $scopeId = null;
        }

        return [$scope, $scopeId];
    }
}