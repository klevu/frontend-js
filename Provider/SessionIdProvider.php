<?php

namespace Klevu\FrontendJs\Provider;

use Klevu\FrontendJs\Api\SessionIdProviderInterface;

class SessionIdProvider implements SessionIdProviderInterface
{
    /**
     * @return string
     */
    public function execute()
    {
        // @todo Replace with more secure hash mechanism
        return md5(session_id());
    }
}
