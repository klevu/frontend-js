<?php

namespace Klevu\FrontendJs\Test\Integration\Block\Html\Head;

use Klevu\FrontendJs\Block\Html\Head\JsPreConnect;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\AbstractController as AbstractControllerTestCase;

class JsPreConnectTest extends AbstractControllerTestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @magentoConfigFixture default/klevu_search/general/js_url other.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/js_url js.klevu.com
     */
    public function testGetPreConnectUrlReturnsKlevuJsUrl()
    {
        $this->setupPhp5();

        $storeManager = $this->objectManager->get(StoreManagerInterface::class);
        $scopeConfig = $this->objectManager->get(ScopeConfigInterface::class);
        $context = $this->objectManager->create(Context::class, [
            'storeManager' => $storeManager,
            'scopeConfig' => $scopeConfig
        ]);

        $jsPreConnect = $this->objectManager->create(JsPreConnect::class, [
            'context' => $context
        ]);
        $expectedUrl = 'https://js.klevu.com';

        $this->assertSame($expectedUrl, $jsPreConnect->getPreConnectUrl());
    }

    /**
     * @magentoAppArea frontend
     * @magentoCache all disabled
     * @magentoConfigFixture default/klevu_search/general/js_url other.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/js_url js.klevu.com
     */
    public function testPreConnectIsOutPutToPage()
    {
        $this->setupPhp5();

        $this->dispatch('/');

        $response = $this->getResponse();
        $this->assertSame(200, $response->getHttpResponseCode());
        $responseBody = $response->getBody();

        $jsPreconnectMatches = [];
        preg_match(
            '#<link rel="preconnect"\s*href="https://js.klevu.com"\s*crossorigin="anonymous"/>#',
            $responseBody,
            $jsPreconnectMatches
        );
        $this->assertNotCount(
            0,
            $jsPreconnectMatches,
            'JS preconnect is present in response body (js.klevu.com)'
        );

        $jsStatsPreconnectMatches = [];
        preg_match(
            '#<link rel="preconnect"\s*href="https://statsjs.klevu.com"\s*crossorigin="anonymous"/>#',
            $responseBody,
            $jsStatsPreconnectMatches
        );
        $this->assertNotCount(
            0,
            $jsStatsPreconnectMatches,
            'JS preconnect is present in response body (statsjs.klevu.com)'
        );
    }

    /**
     * @return void
     */
    private function setupPhp5()
    {
        $this->objectManager = ObjectManager::getInstance();
    }
}
