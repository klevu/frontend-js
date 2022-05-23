<?php

namespace Klevu\FrontendJs\Block\Html\Head;

use Klevu\FrontendJs\Service\IfConfigEvaluator;
use Klevu\FrontendJs\Service\IsEnabledDeterminer;
use Klevu\FrontendJs\Service\JsIncludesSorter;
use Klevu\FrontendJs\Traits\CurrentStoreIdTrait;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;

class JsIncludes extends Template
{
    const URL_PLACEHOLDER_REGEX_SCOPE_CONFIG = '#(\{\{\s*(?<configPath>[a-zA-Z0-9_]+/[a-zA-Z0-9_]+/[a-zA-Z0-9_]+)\s*\}\})#';

    use CurrentStoreIdTrait;

    /**
     * @var JsIncludesSorter
     */
    private $jsIncludesSorter;

    /**
     * @var string[]
     */
    private $jsIncludesUrls;

    /**
     * @var IsEnabledDeterminer
     */
    private $isEnabledDeterminer;

    /**
     * @var IfConfigEvaluator
     */
    private $ifConfigEvaluator;

    /**
     * @param Context $context
     * @param JsIncludesSorter $jsIncludesSorter
     * @param IsEnabledDeterminer $isEnabledDeterminer
     * @param array $data
     * @param IfConfigEvaluator|null $ifConfigEvaluator
     */
    public function __construct(
        Context $context,
        JsIncludesSorter $jsIncludesSorter,
        IsEnabledDeterminer $isEnabledDeterminer,
        array $data = [],
        IfConfigEvaluator $ifConfigEvaluator = null
    ) {
        parent::__construct($context, $data);

        $this->jsIncludesSorter = $jsIncludesSorter;
        $this->isEnabledDeterminer = $isEnabledDeterminer;
        $this->ifConfigEvaluator = $ifConfigEvaluator ?: ObjectManager::getInstance()->get(IfConfigEvaluator::class);
    }

    /**
     * @return string[]
     */
    public function getJsIncludesUrls()
    {
        if (null === $this->jsIncludesUrls) {
            $this->jsIncludesUrls = [];

            $providedJsIncludes = $this->getData('js_includes');
            if (!is_array($providedJsIncludes)) {
                $this->_logger->error(sprintf(
                    'js_includes argument must be array; %s provided',
                    // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
                    gettype($providedJsIncludes)
                ), ['class' => __CLASS__]);

                return $this->jsIncludesUrls;
            }

            $providedJsIncludesFiltered = $this->filterJsIncludes($providedJsIncludes);

            $this->jsIncludesUrls = array_filter(array_unique(array_map(function ($providedJsInclude) {
                if (!isset($providedJsInclude['url']) || !is_string($providedJsInclude['url'])) {
                    return null;
                }

                return $this->replaceUrlPlaceholders(
                    trim($providedJsInclude['url'])
                );
            }, $this->jsIncludesSorter->execute($providedJsIncludesFiltered))));
        }

        return $this->jsIncludesUrls;
    }

    /**
     * @param array $jsIncludes
     * @return array
     */
    private function filterJsIncludes(array $jsIncludes)
    {
        return array_filter($jsIncludes, function ($jsInclude) {
            if (!is_array($jsInclude)) {
                return false;
            }

            $url = isset($jsInclude['url']) ? $jsInclude['url'] : null;
            if (!is_string($url) || !trim($url)) {
                return false;
            }

            if (empty($jsInclude['if_config'])) {
                return true;
            }

            $currentStoreId = $this->getCurrentStoreId($this->_storeManager, $this->_logger);
            if (null === $currentStoreId) {
                return false;
            }

            if (is_string($jsInclude['if_config'])) {
                return $this->_scopeConfig->isSetFlag(
                    $jsInclude['if_config'],
                    ScopeInterface::SCOPE_STORES,
                    $currentStoreId
                );
            }

            $return = true;
            if (is_array($jsInclude['if_config'])) {
                foreach ($jsInclude['if_config'] as $ifConfig) {
                    if (empty($ifConfig['path'])
                        || !isset($ifConfig['conditions'])
                        || !is_array($ifConfig['conditions'])) {
                        continue;
                    }

                    $configValue = $this->_scopeConfig->getValue(
                        $ifConfig['path'],
                        ScopeInterface::SCOPE_STORES,
                        $currentStoreId
                    );

                    $return = $return && $this->ifConfigEvaluator->execute($configValue, $ifConfig['conditions']);
                }
            }

            return $return;
        });
    }

    /**
     * @param string $url
     * @param string $regexScopeConfig
     * @return string
     */
    public function replaceUrlPlaceholders($url, $regexScopeConfig = self::URL_PLACEHOLDER_REGEX_SCOPE_CONFIG)
    {
        $configPathMatches = [];
        preg_match_all($regexScopeConfig, $url, $configPathMatches);
        if (empty($configPathMatches[0]) || empty($configPathMatches['configPath'])) {
            return $url;
        }

        try {
            $store = $this->_storeManager->getStore();
            $storeId = (int)$store->getId();
        } catch (NoSuchEntityException $e) {
            $this->_logger->error($e->getMessage());
            $storeId = null;
        }

        $configPathReplace = [];
        foreach ($configPathMatches[0] as $replaceKey => $replaceString) {
            $configPathReplace[$replaceString] = (string)$this->_scopeConfig->getValue(
                $configPathMatches['configPath'][$replaceKey],
                ScopeInterface::SCOPE_STORES,
                $storeId
            );
        }

        return str_replace(
            array_keys($configPathReplace),
            array_values($configPathReplace),
            $url
        );
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    protected function _toHtml()
    {
        $currentStoreId = $this->getCurrentStoreId($this->_storeManager, $this->_logger);
        if (!$this->isEnabledDeterminer->execute($currentStoreId)) {
            return '';
        }

        return parent::_toHtml();
    }
}
