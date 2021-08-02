<?php

namespace Klevu\FrontendJs\Test\Unit\Service;

use Klevu\FrontendJs\Service\JsIncludesSorter;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class JsIncludesSorterTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * Tests reordering of JS Includes array based on before / after options
     */
    public function testExecute()
    {
        $this->setupPhp5();

        $fixtures = [
            'test' => [
                'url' => 'https://js.klevu.com/core/v2/test.js',
                'after' => 'test2',

            ],
            'test3' => [
                'url' => 'https://js.klevu.com/core/v2/test3.js',
                'after' => 'lib',
            ],
            'test2' => [
                'url' => 'https://js.klevu.com/core/v2/test2.js',
            ],
            'lib' => [
                'url' => 'https://js.klevu.com/core/v2/klevu.js',
                'before' => '-',
            ],
        ];

        /** @var JsIncludesSorter $jsIncludesSorter */
        $jsIncludesSorter = $this->objectManager->getObject(JsIncludesSorter::class);
        $expectedResult = [
            'https://js.klevu.com/core/v2/klevu.js',
            'https://js.klevu.com/core/v2/test3.js',
            'https://js.klevu.com/core/v2/test2.js',
            'https://js.klevu.com/core/v2/test.js',
        ];
        $actualResult = array_column(
            $jsIncludesSorter->execute($fixtures),
            'url'
        );

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
}
