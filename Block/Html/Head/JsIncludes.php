<?php

namespace Klevu\FrontendJs\Block\Html\Head;

use Klevu\FrontendJs\Service\IsEnabledDeterminer;
use Klevu\FrontendJs\Service\JsIncludesSorter;
use Klevu\FrontendJs\Traits\CurrentStoreIdTrait;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;

class JsIncludes extends Template
{
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
     * JsIncludes constructor.
     * @param Context $context
     * @param JsIncludesSorter $jsIncludesSorter
     * @param IsEnabledDeterminer $isEnabledDeterminer
     * @param array $data
     */
    public function __construct(
        Context $context,
        JsIncludesSorter $jsIncludesSorter,
        IsEnabledDeterminer $isEnabledDeterminer,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->jsIncludesSorter = $jsIncludesSorter;
        $this->isEnabledDeterminer = $isEnabledDeterminer;
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

            $this->jsIncludesUrls = array_filter(array_unique(array_map(static function ($providedJsInclude) {
                if (!isset($providedJsInclude['url']) || !is_string($providedJsInclude['url'])) {
                    return null;
                }

                return trim($providedJsInclude['url']);
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

            if (!empty($jsInclude['if_config'])) {
                $currentStoreId = $this->getCurrentStoreId($this->_storeManager, $this->_logger);
                if (null === $currentStoreId ||
                    !$this->_scopeConfig->isSetFlag($jsInclude['if_config'], ScopeInterface::SCOPE_STORES, $currentStoreId)) {
                    return false;
                }
            }

            return true;
        });
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
