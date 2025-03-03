<?php

// phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

namespace Klevu\FrontendJs\Test\Integration\Block;

use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController as AbstractControllerTestCase;

class HomePageOutputTest extends AbstractControllerTestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @magentoAppArea frontend
     * @magentoCache all disabled
     * @magentoConfigFixture default/klevu_frontendjs/configuration/enabled 1
     * @magentoConfigFixture default_store klevu_frontendjs/configuration/enabled 1
     * @noinspection PhpParamsInspection
     */
    public function testJsIncludesAreOutputToPageWhenEnabled()
    {
        $this->setupPhp5();

        $this->dispatch('/');

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        $jsCoreMatches = [];
        preg_match(
            '#<script\s*type="text&\#x2F;javascript"\s*src="https&\#x3A;&\#x2F;&\#x2F;js\.klevu\.com&\#x2F;core&\#x2F;v2&\#x2F;klevu\.js"\s*></script>#',
            $responseBody,
            $jsCoreMatches
        );
        $this->assertNotCount(
            0,
            $jsCoreMatches,
            'Library JS include is present in response body'
        );

        $jsInteractiveMatches = [];
        preg_match(
            '#<script\s*type="text&\#x2F;javascript"\s*id="klevu_jsinteractive">#',
            $responseBody,
            $jsInteractiveMatches
        );
        $this->assertNotCount(
            0,
            $jsInteractiveMatches,
            'Initialisation script is present in response body'
        );
    }

    /**
     * @magentoAppArea frontend
     * @magentoCache all disabled
     * @magentoConfigFixture default/klevu_frontendjs/configuration/enabled 1
     * @magentoConfigFixture default_store klevu_frontendjs/configuration/enabled 1
     * @magentoConfigFixture default/klevu_frontendjs/configuration/defer_js 1
     * @magentoConfigFixture default_store klevu_frontendjs/configuration/defer_js 1
     * @noinspection PhpParamsInspection
     */
    public function testJsIncludesAreOutputToPageWhenEnabled_AndJsDeferred()
    {
        $this->setupPhp5();

        $this->dispatch('/');

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        $jsCoreMatches = [];
        preg_match(
            '#<script\s*type="text&\#x2F;javascript"\s*src="https&\#x3A;&\#x2F;&\#x2F;js\.klevu\.com&\#x2F;core&\#x2F;v2&\#x2F;klevu\.js"\s*defer="defer"\s*></script>#',
            $responseBody,
            $jsCoreMatches
        );
        $this->assertNotCount(
            0,
            $jsCoreMatches,
            'Library JS include is present in response body'
        );

        $jsInteractiveMatches = [];
        preg_match(
            '#<script\s*type="text&\#x2F;javascript"\s*id="klevu_jsinteractive">#',
            $responseBody,
            $jsInteractiveMatches
        );
        $this->assertNotCount(
            0,
            $jsInteractiveMatches,
            'Initialisation script is present in response body'
        );
    }

    /**
     * @return void
     * @todo Move to setUp when PHP 5.x is no longer supported
     */
    private function setupPhp5()
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
