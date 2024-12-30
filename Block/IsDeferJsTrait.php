<?php

namespace Klevu\FrontendJs\Block;

use Klevu\FrontendJs\Constants;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

trait IsDeferJsTrait
{
    /**
     * @var bool|null
     */
    private $isDeferJs = null;

    /**
     * @return bool
     */
    public function isDeferJs()
    {
        if (null !== $this->isDeferJs) {
            return $this->isDeferJs;
        }

        if (!isset($this->_storeManager, $this->_scopeConfig, $this->_logger)
            || !($this->_storeManager instanceof StoreManagerInterface)
            || !($this->_scopeConfig instanceof ScopeConfigInterface)
            || !($this->_logger instanceof LoggerInterface)) {
            throw new \LogicException(
                'Invalid Block configuration for IsDeferJsTrait: missing required dependencies'
            );
        }

        try {
            $store = $this->_storeManager->getStore();
        } catch (NoSuchEntityException $exception) {
            $this->_logger->error(
                sprintf(
                    'Cannot defer JS: cannot load current store: %s',
                    $exception->getMessage()
                ),
                [
                    'exception' => $exception,
                    'method' => __METHOD__,
                ]
            );

            return false;
        }

        $this->isDeferJs = $this->_scopeConfig->isSetFlag(
            Constants::XML_PATH_DEFER_JS,
            ScopeInterface::SCOPE_STORE,
            (int)$store->getId()
        );

        return $this->isDeferJs;
    }
}
