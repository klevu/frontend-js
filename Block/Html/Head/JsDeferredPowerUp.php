<?php

namespace Klevu\FrontendJs\Block\Html\Head;

use Klevu\FrontendJs\Api\InteractiveOptionsGeneratorServiceInterface;
use Klevu\FrontendJs\Api\SerializerInterface;
use Klevu\FrontendJs\Service\IsEnabledDeterminer;
use Klevu\FrontendJs\Traits\CurrentStoreIdTrait;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class JsDeferredPowerUp extends Template
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
    private $jsDeferredPowerUpArray;

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
    public function getJsDeferredPowerUpArray()
    {
        if (null === $this->jsDeferredPowerUpArray) {
            $this->jsDeferredPowerUpArray = [];

            $interactiveOptions = $this->interactiveOptionsGeneratorService->execute();
            if (isset($interactiveOptions['powerUp']) && is_array($interactiveOptions['powerUp'])) {
                $this->jsDeferredPowerUpArray = array_filter([
                    'powerUp' => array_filter($interactiveOptions['powerUp'], static function ($value) {
                        return false === $value;
                    }),
                ]);
            }
        }
        return $this->jsDeferredPowerUpArray;
    }

    /**
     * @return string
     */
    public function getJsDeferredPowerUpSerialized()
    {
        return $this->serializer->serialize(
            $this->getJsDeferredPowerUpArray()
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
            || !$this->getJsDeferredPowerUpArray()) {
            return '';
        }

        return parent::_toHtml();
    }
}
