<?php

namespace Klevu\FrontendJs\Traits;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

trait CurrentStoreIdTrait
{
    /**
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface|null $logger
     * @return int|null
     */
    private function getCurrentStoreId(
        StoreManagerInterface $storeManager,
        LoggerInterface $logger = null
    ) {
        try {
            $store = $storeManager->getStore();
        } catch (NoSuchEntityException $e) {
            if ($logger) {
                $logger->error($e->getMessage(), ['originalException' => $e]);
            }

            return null;
        }

        return (int)$store->getId();
    }
}
