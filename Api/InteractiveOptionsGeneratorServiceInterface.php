<?php

namespace Klevu\FrontendJs\Api;

interface InteractiveOptionsGeneratorServiceInterface
{
    /**
     * @param int|null $storeId
     * @return array
     */
    public function execute($storeId = null);
}
