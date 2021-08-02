<?php

namespace Klevu\FrontendJs\Api;

interface IsEnabledConditionInterface
{
    /**
     * @param int|null $storeId
     * @return bool
     */
    public function execute($storeId = null);
}
