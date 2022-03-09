<?php

namespace Klevu\FrontendJs\Observer;

use Klevu\FrontendJs\Api\SerializerInterface;
use Klevu\FrontendJs\Constants;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Session\Config as SessionConfig;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class InvalidateCustomerDataObserver implements ObserverInterface
{
    const DEFAULT_COOKIE_PATH = '/';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     * @param StoreManagerInterface $storeManager
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer,
        StoreManagerInterface $storeManager,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->storeManager = $storeManager;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    /**
     * @param Observer $observer
     * @return void
     *
     */
    public function execute(Observer $observer)
    {
        $cookieValue = $this->getCurrentCookieValue();
        $cookieValue[Constants::COOKIE_EXPIRE_SECTIONS_KEY][Constants::LOCAL_STORAGE_CUSTOMER_DATA_KEY] = -1;

        try {
            $this->cookieManager->setPublicCookie(
                Constants::COOKIE_KEY,
                $this->serializer->serialize($cookieValue),
                $this->getCookieMetadata()
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @return array
     */
    private function getCurrentCookieValue()
    {
        try {
            $return = $this->serializer->unserialize(
                $this->cookieManager->getCookie(Constants::COOKIE_KEY)
            );
        } catch (\InvalidArgumentException $e) {
            $return = null;
        }
        if (!is_array($return)) {
            $return = [];
        }
        if (!isset($return[Constants::COOKIE_EXPIRE_SECTIONS_KEY])
            || !is_array($return[Constants::COOKIE_EXPIRE_SECTIONS_KEY])) {
            $return[Constants::COOKIE_EXPIRE_SECTIONS_KEY] = [];
        }

        return $return;
    }

    /**
     * @return PublicCookieMetadata
     */
    private function getCookieMetadata()
    {
        $return = $this->cookieMetadataFactory->createPublicCookieMetadata();
        $return->setDuration($this->scopeConfig->getValue(
            SessionConfig::XML_PATH_COOKIE_LIFETIME,
            ScopeInterface::SCOPE_STORES,
            $this->getStoreId()
        ) ?: Constants::DEFAULT_COOKIE_LIFETIME);
        $return->setPath($this->scopeConfig->getValue(
            SessionConfig::XML_PATH_COOKIE_PATH,
            ScopeInterface::SCOPE_STORES,
            $this->getStoreId()
        ) ?: Constants::DEFAULT_COOKIE_PATH);
        $return->setHttpOnly(false);

        return $return;
    }

    /**
     * @return int
     */
    private function getStoreId()
    {
        if (null === $this->storeId) {
            try {
                $store = $this->storeManager->getStore();
                $this->storeId = (int)$store->getId();
            } catch (NoSuchEntityException $e) {
                $this->logger->error($e->getMessage());
                $this->storeId = 0;
            }
        }

        return $this->storeId;
    }
}
