<?php

namespace Klevu\FrontendJs\Api;

interface SessionIdProviderInterface
{
    /**
     * @return string
     */
    public function execute();
}
