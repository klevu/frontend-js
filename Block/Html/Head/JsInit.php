<?php

namespace Klevu\FrontendJs\Block\Html\Head;

use Klevu\FrontendJs\Api\InteractiveOptionsGeneratorServiceInterface;
use Klevu\FrontendJs\Api\SerializerInterface;
use Klevu\FrontendJs\Block\IsDeferJsTrait;
use Klevu\FrontendJs\Service\IsEnabledDeterminer;
use Klevu\FrontendJs\Traits\CurrentStoreIdTrait;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class JsInit extends Template
{
    use CurrentStoreIdTrait;
    use IsDeferJsTrait;

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
    private $interactiveOptions;

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
    public function getInteractiveOptions()
    {
        if (null === $this->interactiveOptions) {
            $this->interactiveOptions = $this->interactiveOptionsGeneratorService->execute();
            array_walk($this->interactiveOptions, static function (&$sectionSettings) {
                $sectionSettings = array_filter($sectionSettings, static function ($key) {
                    return 'apiKey' !== $key;
                }, ARRAY_FILTER_USE_KEY);
            });

            if (isset($this->interactiveOptions['powerUp'])) {
                $this->interactiveOptions['powerUp'] = array_filter(
                    $this->interactiveOptions['powerUp'],
                    static function ($optionValue) {
                        return false !== $optionValue;
                    }
                );
            }

            $this->interactiveOptions = array_filter($this->interactiveOptions);
        }

        return $this->interactiveOptions;
    }

    /**
     * @return string
     */
    public function getInteractiveOptionsSerialized()
    {
        return $this->serializer->serialize(
            $this->getInteractiveOptions()
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
