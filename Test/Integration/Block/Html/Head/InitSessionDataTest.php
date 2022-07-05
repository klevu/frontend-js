<?php

namespace Klevu\FrontendJs\Test\Integration\Block\Html\Head;

use Klevu\FrontendJs\Block\Html\Head\InitSessionData;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class InitSessionDataTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @magentoDbIsolation disabled
     * @magentoDataFixture loadWebsiteFixtures
     * @magentoConfigFixture klevu_test_store_1_store web/unsecure/base_link_url https://test.url/
     * @magentoConfigFixture klevu_test_store_1_store web/secure/base_link_url https://test.url/
     * @magentoConfigFixture klevu_test_store_1_store web/seo/use_rewrites 1
     * @magentoConfigFixture klevu_test_store_1_store web/url/use_store 0
     */
    public function testGetCustomerDataApiEndpointReturnsFullUrl()
    {
        $this->setupPhp5();

        $initSessionData = $this->initSessionData();
        $apiEndpoint = $initSessionData->getCustomerDataApiEndpoint();
        $expectedEndpoint = 'https://test.url/rest/V1/klevu/customerData';

        $this->assertSame($expectedEndpoint, $apiEndpoint);

        static::loadWebsiteFixturesRollback();
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoDataFixture loadWebsiteFixtures
     * @magentoConfigFixture klevu_test_store_1_store web/unsecure/base_link_url https://test.url/
     * @magentoConfigFixture klevu_test_store_1_store web/secure/base_link_url https://test.url/
     * @magentoConfigFixture klevu_test_store_1_store web/seo/use_rewrites 1
     * @magentoConfigFixture klevu_test_store_1_store web/url/use_store 1
     */
    public function testGetCustomerDataApiEndpointReturnsFullUrlWithStoreCode()
    {
        $this->setupPhp5();

        $initSessionData = $this->initSessionData();
        $apiEndpoint = $initSessionData->getCustomerDataApiEndpoint();
        $expectedEndpoint = 'https://test.url/klevu_test_store_1/rest/V1/klevu/customerData';

        $this->assertSame($expectedEndpoint, $apiEndpoint);

        static::loadWebsiteFixturesRollback();
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoDataFixture loadWebsiteFixtures
     * @magentoConfigFixture klevu_test_store_1_store web/unsecure/base_link_url https://test.url/
     * @magentoConfigFixture klevu_test_store_1_store web/secure/base_link_url https://test.url/
     * @magentoConfigFixture klevu_test_store_1_store web/seo/use_rewrites 0
     * @magentoConfigFixture klevu_test_store_1_store web/url/use_store 1
     */
    public function testGetCustomerDataApiEndpointReturnsFullUrlWithStoreCodeNoRewrites()
    {
        $this->setupPhp5();

        $initSessionData = $this->initSessionData();
        $apiEndpoint = $initSessionData->getCustomerDataApiEndpoint();
        $expectedEndpoint = 'https://test.url/index.php/klevu_test_store_1/rest/V1/klevu/customerData';

        $this->assertSame($expectedEndpoint, $apiEndpoint);

        static::loadWebsiteFixturesRollback();
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoDataFixture loadWebsiteFixtures
     * @magentoConfigFixture klevu_test_store_1_store web/unsecure/base_link_url https://test.url/
     * @magentoConfigFixture klevu_test_store_1_store web/secure/base_link_url https://test.url/
     * @magentoConfigFixture klevu_test_store_1_store web/seo/use_rewrites 0
     * @magentoConfigFixture klevu_test_store_1_store web/url/use_store 0
     */
    public function testGetCustomerDataApiEndpointReturnsFullUrlWithNoRewrites()
    {
        $this->setupPhp5();

        $initSessionData = $this->initSessionData();
        $apiEndpoint = $initSessionData->getCustomerDataApiEndpoint();
        $expectedEndpoint = 'https://test.url/index.php/rest/V1/klevu/customerData';

        $this->assertSame($expectedEndpoint, $apiEndpoint);

        static::loadWebsiteFixturesRollback();
    }

    /**
     * @return InitSessionData|mixed
     * @throws NoSuchEntityException
     */
    private function initSessionData()
    {
        $store = $this->getStore();
        $mockStoreManager = $this->getMockBuilder(StoreManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockStoreManager->expects($this->once())->method('getStore')->willReturn($store);

        $scopeConfig = $this->objectManager->get(ScopeConfigInterface::class);

        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $context->expects($this->once())->method('getStoreManager')->willReturn($mockStoreManager);
        $context->expects($this->once())->method('getScopeConfig')->willReturn($scopeConfig);

        $initSessionData = $this->objectManager->create(InitSessionData::class, [
            'context' => $context
        ]);

        return $initSessionData;
    }

    /**
     * @param string $storeCode
     *
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    private function getStore($storeCode = 'klevu_test_store_1')
    {
        $storeManager = $this->objectManager->get(StoreManagerInterface::class);

        return $storeManager->getStore($storeCode);
    }

    /**
     * @return void
     */
    private function setupPhp5()
    {
        $this->objectManager = ObjectManager::getInstance();
    }

    /**
     * Loads website collection creation scripts because annotations use a relative path
     *  from integration tests root
     */
    public static function loadWebsiteFixtures()
    {
        require __DIR__ . '/../../../_files/websiteFixtures.php';
    }

    /**
     * Rolls back order creation scripts because annotations use a relative path
     *  from integration tests root
     */
    public static function loadWebsiteFixturesRollback()
    {
        require __DIR__ . '/../../../_files/websiteFixtures_rollback.php';
    }
}