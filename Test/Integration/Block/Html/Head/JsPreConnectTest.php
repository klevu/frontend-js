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

        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString(
                '<link rel="preconnect" href="https://js.klevu.com"/>',
                $responseBody,
                'JS preconnect is present in response body'
            );
        } else {
            $this->assertContains(
                '<link rel="preconnect" href="https://js.klevu.com"/>',
                $responseBody,
                'JS preconnect is present in response body'
            );
        }
    }

    /**
     * @return void
     */
    private function setupPhp5()
    {
        $this->objectManager = ObjectManager::getInstance();
    }
}