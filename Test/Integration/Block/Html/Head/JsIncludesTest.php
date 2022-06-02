<?php

namespace Klevu\FrontendJs\Test\Integration\Block\Html\Head;

use Klevu\FrontendJs\Block\Html\Head\JsIncludes;
use Magento\Framework\View\Element\Template\Context;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class JsIncludesTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @magentoConfigFixture default/klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/js_url js-test.klevu.com
     * @magentoConfigFixture default/klevu_search/test/output_flag 0
     */
    public function testGetJsIncludesUrls()
    {
        $this->setupPhp5();

        /** @var JsIncludes $jsIncludesBlock */
        $jsIncludesBlock = $this->objectManager->create(JsIncludes::class);
        $jsIncludesBlock->setData(
            'js_includes',
            [
                'klevu_themev2_srlp' => [
                    'after' => 'klevu_themev2_quicksearch',
                    'url' => 'https://{{ klevu_search/general/js_url }}/theme/default/v2/landing-page-theme.js',
                ],
                'lib' => [
                    'before' => '-',
                    'url' => 'https://{{ klevu_search/general/js_url }}/core/v2/klevu.js',
                ],
                'klevu_themev2_quicksearch' => [
                    'url' => 'https://{{ klevu_search/general/js_url }}/theme/default/v2/quick-search-theme.js',
                ],
                'missing_url' => [
                    'url' => ' ',
                ],
                'configured_do_not_output' => [
                    'url' => 'https://klevu.com/do-not-output',
                    'if_config' => [
                        'output_flag_enabled' => [
                            'path' => 'klevu_search/test/output_flag',
                            'conditions' => [
                                'eq' => '1',
                            ],
                        ],
                    ],
                ],
                'duplicate' => [
                    'url' => 'https://{{ klevu_search/general/js_url }}/core/v2/klevu.js',
                ],
            ]
        );

        $expectedResult = [
            'https://js-test.klevu.com/core/v2/klevu.js',
            'https://js-test.klevu.com/theme/default/v2/quick-search-theme.js',
            'https://js-test.klevu.com/theme/default/v2/landing-page-theme.js',
        ];
        $actualResult = $jsIncludesBlock->getJsIncludesUrls();

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @magentoConfigFixture default/klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/js_url js-test.klevu.com
     * @magentoConfigFixture default/klevu_search/test/output_flag 0
     */
    public function testGetJsIncludesUrls_InvalidType()
    {
        $this->setupPhp5();

        $loggerMock = $this->getLoggerMock();
        $loggerMock->expects($this->atLeastOnce())
            ->method('error');
        $contextMock = $this->getContextMock();
        $contextMock->method('getLogger')
            ->willReturn($loggerMock);

        /** @var JsIncludes $jsIncludesBlock */
        $jsIncludesBlock = $this->objectManager->create(JsIncludes::class, [
            'context' => $contextMock,
        ]);
        $jsIncludesBlock->setData(
            'js_includes',
            'foo'
        );

        $expectedResult = [];
        $actualResult = $jsIncludesBlock->getJsIncludesUrls();

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @magentoConfigFixture default/klevu_search/general/url test-global.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/url test-store.klevu.com
     * @magentoConfigFixture default/klevu_search/general/slug bar
     * @magentoConfigFixture default_store klevu_search/general/slug baz
     */
    public function testReplaceUrlPlaceholders_Default_WithoutPlaceholder()
    {
        $this->setupPhp5();

        /** @var JsIncludes $jsIncludesBlock */
        $jsIncludesBlock = $this->objectManager->create(JsIncludes::class);

        $fixture = 'https://js.klevu.com/foo';

        $expectedResult = 'https://js.klevu.com/foo';
        $actualResult = $jsIncludesBlock->replaceUrlPlaceholders($fixture);

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @magentoConfigFixture default/klevu_search/general/url test-global.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/url test-store.klevu.com
     * @magentoConfigFixture default/klevu_search/general/slug bar
     * @magentoConfigFixture default_store klevu_search/general/slug baz
     */
    public function testReplaceUrlPlaceholders_Default_NoMatchingPlaceholder()
    {
        $this->setupPhp5();

        /** @var JsIncludes $jsIncludesBlock */
        $jsIncludesBlock = $this->objectManager->create(JsIncludes::class);

        $fixture = 'https://[klevu_search/general/url]/foo';

        $expectedResult = 'https://[klevu_search/general/url]/foo';
        $actualResult = $jsIncludesBlock->replaceUrlPlaceholders($fixture);

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @magentoConfigFixture default/klevu_search/general/url test-global.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/url test-store.klevu.com
     * @magentoConfigFixture default/klevu_search/general/slug bar
     * @magentoConfigFixture default_store klevu_search/general/slug baz
     */
    public function testReplaceUrlPlaceholders_Default_SinglePlaceholder()
    {
        $this->setupPhp5();

        /** @var JsIncludes $jsIncludesBlock */
        $jsIncludesBlock = $this->objectManager->create(JsIncludes::class);

        $fixture = 'https://{{ klevu_search/general/url }}/foo';

        $expectedResult = 'https://test-store.klevu.com/foo';
        $actualResult = $jsIncludesBlock->replaceUrlPlaceholders($fixture);

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @magentoConfigFixture default/klevu_search/general/url test-global.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/url test-store.klevu.com
     * @magentoConfigFixture default/klevu_search/general/slug bar
     * @magentoConfigFixture default_store klevu_search/general/slug baz
     */
    public function testReplaceUrlPlaceholders_Default_MultiplePlaceholders()
    {
        $this->setupPhp5();

        /** @var JsIncludes $jsIncludesBlock */
        $jsIncludesBlock = $this->objectManager->create(JsIncludes::class);

        $fixture = 'https://{{ klevu_search/general/url }}/{{klevu_search/general/slug}}';

        $expectedResult = 'https://test-store.klevu.com/baz';
        $actualResult = $jsIncludesBlock->replaceUrlPlaceholders($fixture);

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @magentoConfigFixture default/klevu_search/general/url test-global.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/url test-store.klevu.com
     * @magentoConfigFixture default/klevu_search/general/slug bar
     * @magentoConfigFixture default_store klevu_search/general/slug baz
     */
    public function testReplaceUrlPlaceholders_Custom_WithoutPlaceholder()
    {
        $this->setupPhp5();

        /** @var JsIncludes $jsIncludesBlock */
        $jsIncludesBlock = $this->objectManager->create(JsIncludes::class);

        $fixture = 'https://js.klevu.com/foo';

        $expectedResult = 'https://js.klevu.com/foo';
        $actualResult = $jsIncludesBlock->replaceUrlPlaceholders(
            $fixture,
            '#(\[\s*(?<configPath>[a-zA-Z0-9_]+/[a-zA-Z0-9_]+/[a-zA-Z0-9_]+)\s*\])#'
        );

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @magentoConfigFixture default/klevu_search/general/url test-global.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/url test-store.klevu.com
     * @magentoConfigFixture default/klevu_search/general/slug bar
     * @magentoConfigFixture default_store klevu_search/general/slug baz
     */
    public function testReplaceUrlPlaceholders_Custom_NoMatchingPlaceholder()
    {
        $this->setupPhp5();

        /** @var JsIncludes $jsIncludesBlock */
        $jsIncludesBlock = $this->objectManager->create(JsIncludes::class);

        $fixture = 'https://{{ klevu_search/general/url }}/foo';

        $expectedResult = 'https://{{ klevu_search/general/url }}/foo';
        $actualResult = $jsIncludesBlock->replaceUrlPlaceholders(
            $fixture,
            '#(\[\s*(?<configPath>[a-zA-Z0-9_]+/[a-zA-Z0-9_]+/[a-zA-Z0-9_]+)\s*\])#'
        );

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @magentoConfigFixture default/klevu_search/general/url test-global.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/url test-store.klevu.com
     * @magentoConfigFixture default/klevu_search/general/slug bar
     * @magentoConfigFixture default_store klevu_search/general/slug baz
     */
    public function testReplaceUrlPlaceholders_Custom_SinglePlaceholder()
    {
        $this->setupPhp5();

        /** @var JsIncludes $jsIncludesBlock */
        $jsIncludesBlock = $this->objectManager->create(JsIncludes::class);

        $fixture = 'https://[klevu_search/general/url]/foo';

        $expectedResult = 'https://test-store.klevu.com/foo';
        $actualResult = $jsIncludesBlock->replaceUrlPlaceholders(
            $fixture,
            '#(\[\s*(?<configPath>[a-zA-Z0-9_]+/[a-zA-Z0-9_]+/[a-zA-Z0-9_]+)\s*\])#'
        );

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @magentoConfigFixture default/klevu_search/general/url test-global.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/url test-store.klevu.com
     * @magentoConfigFixture default/klevu_search/general/slug bar
     * @magentoConfigFixture default_store klevu_search/general/slug baz
     */
    public function testReplaceUrlPlaceholders_Custom_MultiplePlaceholders()
    {
        $this->setupPhp5();

        /** @var JsIncludes $jsIncludesBlock */
        $jsIncludesBlock = $this->objectManager->create(JsIncludes::class);

        $fixture = 'https://[klevu_search/general/url]/[klevu_search/general/slug]';

        $expectedResult = 'https://test-store.klevu.com/baz';
        $actualResult = $jsIncludesBlock->replaceUrlPlaceholders(
            $fixture,
            '#(\[\s*(?<configPath>[a-zA-Z0-9_]+/[a-zA-Z0-9_]+/[a-zA-Z0-9_]+)\s*\])#'
        );

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @magentoConfigFixture default/klevu_search/general/url test-global.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/url test-store.klevu.com
     * @magentoConfigFixture default/klevu_search/general/slug bar
     * @magentoConfigFixture default_store klevu_search/general/slug baz
     */
    public function testReplaceUrlPlaceholders_Custom_InvalidRegex()
    {
        $this->setupPhp5();

        /** @var JsIncludes $jsIncludesBlock */
        $jsIncludesBlock = $this->objectManager->create(JsIncludes::class);

        $fixture = 'https://[klevu_search/general/url]/[klevu_search/general/slug]';

        $expectedResult = 'https://[klevu_search/general/url]/[klevu_search/general/slug]';
        $actualResult = $jsIncludesBlock->replaceUrlPlaceholders(
            $fixture,
            '#(\[\s*([a-zA-Z0-9_]+/[a-zA-Z0-9_]+/[a-zA-Z0-9_]+)\s*\])#'
        );

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @return void
     */
    private function setupPhp5()
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    /**
     * @return Context
     */
    private function getContextMock()
    {
        return $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return LoggerInterface
     */
    private function getLoggerMock()
    {
        return $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
