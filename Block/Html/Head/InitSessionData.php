<?php

namespace Klevu\FrontendJs\Block\Html\Head;

use Klevu\FrontendJs\Constants;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Session\Config as SessionConfig;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

/**
 * @todo Use ViewModels when older Magento BC support dropped
 */
class InitSessionData extends Template
{
    /**
     * @var int
     */
    private $storeId;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @return string
     */
    public function getLocalStorageKey()
    {
        return Constants::LOCAL_STORAGE_KEY;
    }

    /**
     * @return string
     */
    public function getCookieKey()
    {
        return Constants::COOKIE_KEY;
    }

    /**
     * @return string
     */
    public function getExpireSectionsKey()
    {
        return Constants::COOKIE_EXPIRE_SECTIONS_KEY;
    }

    /**
     * @return string
     */
    public function getCustomerDataKey()
    {
        return Constants::LOCAL_STORAGE_CUSTOMER_DATA_KEY;
    }

    /**
     * @return string
     */
    public function getCookieLifetime()
    {
        $lifetime = $this->_scopeConfig->getValue(
            SessionConfig::XML_PATH_COOKIE_LIFETIME,
            ScopeInterface::SCOPE_STORES,
            $this->getCurrentStoreId()
        );

        return (int)($lifetime ?: Constants::DEFAULT_COOKIE_LIFETIME);
    }

    /**
     * @return string
     */
    public function getCookiePath()
    {
        $path = $this->_scopeConfig->getValue(
            SessionConfig::XML_PATH_COOKIE_PATH,
            ScopeInterface::SCOPE_STORES,
            $this->getCurrentStoreId()
        );

        return (string)($path ?: Constants::DEFAULT_COOKIE_PATH);
    }

    /**
     * @return int
     */
    public function getCustomerDataSectionTtl()
    {
        return Constants::LOCAL_STORAGE_TTL_CUSTOMER_DATA;
    }

    /**
     * @return string
     */
    public function getCustomerDataLoadedEventName()
    {
        return Constants::JS_EVENTNAME_CUSTOMER_DATA_LOADED;
    }

    /**
     * @return string;
     */
    public function getCustomerDataLoadErrorEventName()
    {
        return Constants::JS_EVENTNAME_CUSTOMER_DATA_LOAD_ERROR;
    }

    /**
     * @return string
     */
    public function getCustomerDataApiEndpoint()
    {
        return $this->getCurrentBaseUrl() . 'rest/V1/klevu/customerData';
    }

    /**
     * @return int
     */
    private function getCurrentStoreId()
    {
        if (null === $this->storeId) {
            try {
                $store = $this->_storeManager->getStore();
                $this->storeId = (int)$store->getId();
            } catch (NoSuchEntityException $e) {
                $this->_logger->error($e->getMessage());
                $this->storeId = 0;
            }
        }

        return $this->storeId;
    }

    /**
     * @return string
     */
    private function getCurrentBaseUrl()
    {
        if (null === $this->baseUrl) {
            try {
                $store = $this->_storeManager->getStore();
                $this->baseUrl = (string)$store->getBaseUrl(UrlInterface::URL_TYPE_LINK);
            } catch (NoSuchEntityException $e) {
                $this->_logger->error($e->getMessage());
                $this->baseUrl = '';
            }
        }

        return $this->baseUrl;
    }
}
