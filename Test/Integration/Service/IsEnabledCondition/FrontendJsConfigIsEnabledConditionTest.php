<?php

namespace Klevu\FrontendJs\Test\Integration\Service\IsEnabledCondition;

use Klevu\FrontendJs\Service\IsEnabledCondition\FrontendJsConfigIsEnabledCondition;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class FrontendJsConfigIsEnabledConditionTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoCache all disabled
     * @magentoConfigFixture default/klevu_frontendjs/configuration/enabled 1
     * @magentoConfigFixture default_store klevu_frontendjs/configuration/enabled 1
     */
    public function testExecuteWhenEnabled()
    {
        $this->setupPhp5();

        /** @var FrontendJsConfigIsEnabledCondition $isEnabledCondition */
        $isEnabledCondition = $this->objectManager->get(FrontendJsConfigIsEnabledCondition::class);

        $this->assertTrue($isEnabledCondition->execute(1));
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoCache all disabled
     * @magentoConfigFixture default/klevu_frontendjs/configuration/enabled 0
     * @magentoConfigFixture default_store klevu_frontendjs/configuration/enabled 0
     */
    public function testExecuteWhenDisabled()
    {
        $this->setupPhp5();

        /** @var FrontendJsConfigIsEnabledCondition $isEnabledCondition */
        $isEnabledCondition = $this->objectManager->get(FrontendJsConfigIsEnabledCondition::class);

        $this->assertFalse($isEnabledCondition->execute(1));
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
