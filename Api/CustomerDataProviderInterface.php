<?php

namespace Klevu\FrontendJs\Api;

interface CustomerDataProviderInterface
{
    /**
     * @return \Klevu\FrontendJs\Api\Data\CustomerDataInterface
     */
    public function execute();
}
