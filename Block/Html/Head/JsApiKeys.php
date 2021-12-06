<?php

namespace Klevu\FrontendJs\Block\Html\Head;

use Klevu\FrontendJs\Api\InteractiveOptionsGeneratorServiceInterface;
use Klevu\FrontendJs\Api\SerializerInterface;
use Klevu\FrontendJs\Service\IsEnabledDeterminer;
use Klevu\FrontendJs\Traits\CurrentStoreIdTrait;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class JsApiKeys extends Template
{
    use CurrentStoreIdTrait;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var InteractiveOptionsGeneratorServiceInterface
     */
    private $interactiveOptionsGeneratorService;

    /**
     * @var IsEnabledDeterminer
     */
    private $isEnabledDeterminer;

    /**
     * @var array
     */
    private $jsApiKeysArray;

    /**
     * @param Context $context
     * @param SerializerInterface $serializer
     * @param InteractiveOptionsGeneratorServiceInterface $interactiveOptionsGeneratorService
     * @param IsEnabledDeterminer $isEnabledDeterminer
     * @param array $data
     */
    public function __construct(
        Context $context,
        SerializerInterface $serializer,
        InteractiveOptionsGeneratorServiceInterface $interactiveOptionsGeneratorService,
        IsEnabledDeterminer $isEnabledDeterminer,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->serializer = $serializer;
        $this->interactiveOptionsGeneratorService = $interactiveOptionsGeneratorService;
        $this->isEnabledDeterminer = $isEnabledDeterminer;
    }

    /**
     * @return array
     */
    public function getJsApiKeysArray()
    {
        if (null === $this->jsApiKeysArray) {
            $interactiveOptions = $this->interactiveOptionsGeneratorService->execute();

            $this->jsApiKeysArray = [];
            foreach ($interactiveOptions as $sectionKey => $sectionSettings) {
                $this->jsApiKeysArray[$sectionKey] = array_intersect_key($sectionSettings, ['apiKey' => null]);
            }
            $this->jsApiKeysArray = array_filter($this->jsApiKeysArray);
        }

        return $this->jsApiKeysArray;
    }

    /**
     * @return string
     */
    public function getJsApiKeysSerialized()
    {
        return $this->serializer->serialize(
            $this->getJsApiKeysArray()
        );
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    protected function _toHtml()
    {
        $currentStoreId = $this->getCurrentStoreId($this->_storeManager, $this->_logger);
        if (!$this->isEnabledDeterminer->execute($currentStoreId)
            || !$this->getJsApiKeysArray()) {
            return '';
        }

        return parent::_toHtml();
    }
}
