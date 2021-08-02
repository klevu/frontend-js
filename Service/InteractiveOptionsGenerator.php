<?php

namespace Klevu\FrontendJs\Service;

use Klevu\FrontendJs\Api\InteractiveOptionsGeneratorServiceInterface;
use Klevu\FrontendJs\Api\InteractiveOptionsProviderInterface;
use Psr\Log\LoggerInterface;

class InteractiveOptionsGenerator implements InteractiveOptionsGeneratorServiceInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var InteractiveOptionsProviderInterface[]
     */
    private $interactiveOptionsProviders = [];

    /**
     * InteractiveOptionsGenerator constructor.
     * @param LoggerInterface $logger
     * @param array $interactiveOptionsProviders
     */
    public function __construct(
        LoggerInterface $logger,
        array $interactiveOptionsProviders = []
    ) {
        $this->logger = $logger;
        array_walk($interactiveOptionsProviders, [$this, 'addInteractiveOptionsProvider']);
    }

    /**
     * @param InteractiveOptionsProviderInterface $interactiveOptionsProvider
     */
    private function addInteractiveOptionsProvider(
        InteractiveOptionsProviderInterface $interactiveOptionsProvider
    ) {
        $interactiveOptionsProviderSplId = spl_object_hash($interactiveOptionsProvider);
        foreach ($this->interactiveOptionsProviders as $existingInteractiveOptionsProvider) {
            if (spl_object_hash($existingInteractiveOptionsProvider) === $interactiveOptionsProviderSplId) {
                return;
            }
        }

        $this->interactiveOptionsProviders[] = $interactiveOptionsProvider;
    }

    /**
     * @param int|null $storeId
     * @return array
     */
    public function execute($storeId = null)
    {
        $return = [];
        foreach ($this->interactiveOptionsProviders as $interactiveOptionsProvider) {
            $options = $interactiveOptionsProvider->execute($storeId);
            if ($options) {
                $return = $this->mergeOptions($return, $options);
            }
        }

        return $return;
    }

    /**
     * @param array $originalOptions
     * @param array $additionalOptions
     * @return array
     */
    private function mergeOptions(array $originalOptions, array $additionalOptions)
    {
        $return = $originalOptions;
        foreach ($additionalOptions as $additionalKey => $additionalValue) {
            if (!is_string($additionalKey)) {
                $return[] = $additionalValue;
                continue;
            }

            switch (true) {
                case !array_key_exists($additionalKey, $return):
                case !is_array($return[$additionalKey]):
                case !is_array($additionalValue):
                    $return[$additionalKey] = $additionalValue;
                    break;

                default:
                    $return[$additionalKey] = $this->mergeOptions($return[$additionalKey], $additionalValue);
                    break;
            }
        }

        return $return;
    }
}
