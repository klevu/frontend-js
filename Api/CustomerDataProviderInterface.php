<?php

namespace Klevu\FrontendJs\Api;

use Klevu\FrontendJs\Api\Data\CustomerDataInterface;

interface CustomerDataProviderInterface
{
    /**
     * @return CustomerDataInterface
     */
    public function execute();
}
