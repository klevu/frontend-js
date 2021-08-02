<?php

namespace Klevu\FrontendJs\Service;

use Klevu\FrontendJs\Api\IsEnabledConditionInterface;

class IsEnabledDeterminer
{
    /**
     * @var IsEnabledConditionInterface[]
     */
    private $isEnabledConditions = [];

    /**
     * IsEnabledDeterminer constructor.
     * @param array $isEnabledConditions
     */
    public function __construct(
        array $isEnabledConditions = []
    ) {
        array_walk($isEnabledConditions, [$this, 'addIsEnabledCondition']);
    }

    /**
     * @param IsEnabledConditionInterface $isEnabledCondition
     * @param string $identifier
     */
    private function addIsEnabledCondition(IsEnabledConditionInterface $isEnabledCondition, $identifier)
    {
        $this->isEnabledConditions[$identifier] = $isEnabledCondition;
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function execute($storeId = null)
    {
        $return = false;

        foreach ($this->isEnabledConditions as $isEnabledCondition) {
            if ($isEnabledCondition->execute($storeId)) {
                $return = true;
                break;
            }
        }

        return $return;
    }
}
