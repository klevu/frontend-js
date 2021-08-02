<?php

namespace Klevu\FrontendJs\Test\Unit\Service;

use Klevu\FrontendJs\Api\IsEnabledConditionInterface;
use Klevu\FrontendJs\Service\IsEnabledDeterminer;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class IsEnabledDeterminerTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @throws \ReflectionException
     */
    public function testExecuteAllEnabledConditions()
    {
        $this->setupPhp5();

        /** @var IsEnabledDeterminer $isEnabledDeterminer */
        $isEnabledDeterminer = $this->objectManager->getObject(IsEnabledDeterminer::class, [
            'isEnabledConditions' => [
                'foo' => $this->getIsEnabledConditionMock(true),
                'bar' => $this->getIsEnabledConditionMock(true),
            ],
        ]);

        $this->assertTrue($isEnabledDeterminer->execute());
    }

    /**
     * @throws \ReflectionException
     */
    public function testExecuteAllDisabledConditions()
    {
        $this->setupPhp5();

        /** @var IsEnabledDeterminer $isEnabledDeterminer */
        $isEnabledDeterminer = $this->objectManager->getObject(IsEnabledDeterminer::class, [
            'isEnabledConditions' => [
                'foo' => $this->getIsEnabledConditionMock(false),
                'bar' => $this->getIsEnabledConditionMock(false),
            ],
        ]);

        $this->assertFalse($isEnabledDeterminer->execute());
    }

    /**
     * @throws \ReflectionException
     */
    public function testExecuteMixedConditionsTrueFirst()
    {
        $this->setupPhp5();

        /** @var IsEnabledDeterminer $isEnabledDeterminer */
        $isEnabledDeterminer = $this->objectManager->getObject(IsEnabledDeterminer::class, [
            'isEnabledConditions' => [
                'foo' => $this->getIsEnabledConditionMock(true),
                'bar' => $this->getIsEnabledConditionMock(false),
            ],
        ]);

        $this->assertTrue($isEnabledDeterminer->execute());
    }

    /**
     * @throws \ReflectionException
     */
    public function testExecuteMixedConditionsFalseFirst()
    {
        $this->setupPhp5();

        /** @var IsEnabledDeterminer $isEnabledDeterminer */
        $isEnabledDeterminer = $this->objectManager->getObject(IsEnabledDeterminer::class, [
            'isEnabledConditions' => [
                'foo' => $this->getIsEnabledConditionMock(false),
                'bar' => $this->getIsEnabledConditionMock(true),
            ],
        ]);

        $this->assertTrue($isEnabledDeterminer->execute());
    }

    /**
     * @param bool $return
     * @return IsEnabledConditionInterface|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getIsEnabledConditionMock($return)
    {
        if (!method_exists($this, 'createMock')) {
            return $this->getIsEnabledConditionMockLegacy($return);
        }

        $isEnabledConditionMock = $this->createMock(IsEnabledConditionInterface::class);
        $isEnabledConditionMock->method('execute')->willReturn($return);

        return $isEnabledConditionMock;
    }

    /**
     * @param bool $return
     * @return IsEnabledConditionInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getIsEnabledConditionMockLegacy($return)
    {
        $isEnabledConditionMock = $this->getMockBuilder(IsEnabledConditionInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $isEnabledConditionMock->expects($this->any())
            ->method('execute')
            ->willReturn($return);

        return $isEnabledConditionMock;
    }

    /**
     * @return void
     * @todo Move to setUp when PHP 5.x is no longer supported
     */
    private function setupPhp5()
    {
        $this->objectManager = new ObjectManager($this);
    }
}
