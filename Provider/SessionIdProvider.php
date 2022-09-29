<?php

namespace Klevu\FrontendJs\Provider;

use Klevu\FrontendJs\Api\SessionIdProviderInterface;
use Klevu\Search\Provider\Customer\SessionIdProvider as SearchSessionIdProvider;

/**
 * @deprecated in favour of \Klevu\Search\Provider\Customer\SessionIdProvider
 */
class SessionIdProvider implements SessionIdProviderInterface
{
    /**
     * @var SearchSessionIdProvider
     */
    private $sessionIdProvider;

    public function __construct(
        SearchSessionIdProvider $sessionIdProvider
    ) {
        $this->sessionIdProvider = $sessionIdProvider;
    }

    /**
     * @return string
     * @deprecated in favour of \Klevu\Search\Provider\Customer\SessionIdProvider::execute()
     */
    public function execute()
    {
        return $this->sessionIdProvider->execute();
    }
}
