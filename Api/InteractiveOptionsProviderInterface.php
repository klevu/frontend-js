<?php

namespace Klevu\FrontendJs\Api;

interface InteractiveOptionsProviderInterface
{
    /**
     * @param int|null $storeId
     * @return array
     */
    public function execute($storeId = null);
}
