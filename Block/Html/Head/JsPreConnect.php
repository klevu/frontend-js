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
     * @return string[]
     */
    public function getPreConnectUrls()
    {
        return array_filter(
            array_unique(
                array_merge(
                    isset($this->_data['pre_connect_urls']) ? $this->_data['pre_connect_urls'] : [],
                    [
                        $this->getPreConnectUrl(),
                    ]
                )
            )
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
