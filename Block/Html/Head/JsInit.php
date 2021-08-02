<?php

namespace Klevu\FrontendJs\Block\Html\Head;

use Klevu\FrontendJs\Api\InteractiveOptionsGeneratorServiceInterface;
use Klevu\FrontendJs\Api\SerializerInterface;
use Klevu\FrontendJs\Service\IsEnabledDeterminer;
use Klevu\FrontendJs\Traits\CurrentStoreIdTrait;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class JsInit extends Template
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
     * JsInit constructor.
     * @param Context $context
     * @param SerializerInterface $serializer
     * @param InteractiveOptionsGeneratorServiceInterface $interactiveOptionsGeneratorService
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
        return $this->interactiveOptionsGeneratorService->execute();
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
