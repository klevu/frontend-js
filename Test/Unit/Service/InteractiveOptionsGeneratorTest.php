<?php

namespace Klevu\FrontendJs\Test\Unit\Service;

use Klevu\FrontendJs\Api\InteractiveOptionsProviderInterface;
use Klevu\FrontendJs\Service\InteractiveOptionsGenerator;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class InteractiveOptionsGeneratorTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * Tests receiving configuration from options providers and merging into single options
     *  array according to merge rules
     */
    public function testExecute()
    {
        $this->setupPhp5();

        /** @var InteractiveOptionsGenerator $interactiveOptionsGenerator */
        $interactiveOptionsGenerator = $this->objectManager->getObject(InteractiveOptionsGenerator::class, [
            'interactiveOptionsProviders' => [
                $this->getInteractiveOptionsProviderMock([
                    'powerUp' => [
                        'recsModule' => true,
                    ],
                    'recs' => [
                        'apiKey' => 'klevu-12345',
                    ],
                    'nested' => [
                        'nested2_1' => [
                            'nested3' => 'a',
                        ],
                        'nested2_2' => [
                            'foo' => 'bar',
                            'wom',
                        ],
                    ],
                ]),
                $this->getInteractiveOptionsProviderMock([
                    'recs' => [
                        'apiKey' => 'klevu-67890',
                    ],
                    'foo' => 'bar',
                    'url' => [
                        'fooSource' => '//js.klevu.com/foo/',
                    ],
                ]),
                $this->getInteractiveOptionsProviderMock([
                    'url' => [
                        'recsSource' => '//js.klevu.com/recs/',
                        'recsEndpoint' => '//rest.ksearchnet.com/recommendations/',
                    ],
                    'powerUp' => [
                        'foo' => false,
                    ],
                    'foo' => [
                        'baz',
                    ],
                    'nested' => [
                        'nested2_1' => [
                            'nested3_2' => [
                                'bar',
                            ],
                        ],
                        'nested2_2' => [
                            'foo' => 'baz',
                            'bat',
                        ],
                    ],
                ]),
            ],
        ]);

        $expectedResult = [
            'powerUp' => [
                'recsModule' => true,
                'foo' => false,
            ],
            'recs' => [
                'apiKey' => 'klevu-67890',
            ],
            'nested' => [
                'nested2_1' => [
                    'nested3' => 'a',
                    'nested3_2' => [
                        'bar',
                    ],
                ],
                'nested2_2' => [
                    'foo' => 'baz',
                    'wom',
                    'bat',
                ],
            ],
            'foo' => [
                'baz',
            ],
            'url' => [
                'fooSource' => '//js.klevu.com/foo/',
                'recsSource' => '//js.klevu.com/recs/',
                'recsEndpoint' => '//rest.ksearchnet.com/recommendations/',
            ],
        ];
        $actualResult = $interactiveOptionsGenerator->execute();

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @return void
     * @todo Move to setUp when PHP 5.x is no longer supported
     */
    private function setupPhp5()
    {
        $this->objectManager = new ObjectManager($this);
    }

    /**
     * @param array $return
     * @return InteractiveOptionsProviderInterface|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getInteractiveOptionsProviderMock(array $return)
    {
        if (!method_exists($this, 'createMock')) {
            return $this->getInteractiveOptionsProviderMockLegacy($return);
        }

        $interactiveOptionsProviderMock = $this->createMock(InteractiveOptionsProviderInterface::class);
        $interactiveOptionsProviderMock->method('execute')->willReturn($return);

        return $interactiveOptionsProviderMock;
    }

    /**
     * @param array $return
     * @return InteractiveOptionsProviderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getInteractiveOptionsProviderMockLegacy(array $return)
    {
        $interactiveOptionsProviderMock = $this->getMockBuilder(InteractiveOptionsProviderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $interactiveOptionsProviderMock->expects($this->any())
            ->method('execute')
            ->willReturn($return);

        return $interactiveOptionsProviderMock;
    }
}
