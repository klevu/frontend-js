<?php

namespace Klevu\FrontendJs\Service;

use Psr\Log\LoggerInterface;

class IfConfigEvaluator
{
    const CONDITION_IN = 'in';
    const CONDITION_NIN = 'nin';
    const CONDITION_EQ = 'eq';
    const CONDITION_NEQ = 'neq';
    const CONDITION_GT = 'gt';
    const CONDITION_LT = 'lt';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @param mixed $value
     * @param array $conditions
     * @return bool
     */
    public function execute($value, array $conditions)
    {
        return $this->evaluateIn($value, $conditions)
            && $this->evaluateNin($value, $conditions)
            && $this->evaluateEq($value, $conditions)
            && $this->evaluateNeq($value, $conditions)
            && $this->evaluateGt($value, $conditions)
            && $this->evaluateLt($value, $conditions);
    }

    /**
     * @param mixed $value
     * @param array $conditions
     * @return bool
     */
    private function evaluateIn($value, array $conditions)
    {
        if (!array_key_exists(static::CONDITION_IN, $conditions)) {
            return true;
        }

        if (!is_array($conditions[static::CONDITION_IN])) {
            $this->logger->warning(sprintf(
                'if_config IN condition must be of type array; %s received',
                gettype($conditions[static::CONDITION_IN])
            ), [
                'value' => $value,
                'conditions' => $conditions,
            ]);

            return false;
        }

        return in_array($value, $conditions[static::CONDITION_IN]);
    }

    /**
     * @param mixed $value
     * @param array $conditions
     * @return bool
     */
    private function evaluateNin($value, array $conditions)
    {
        if (!array_key_exists(static::CONDITION_NIN, $conditions)) {
            return true;
        }

        if (!is_array($conditions[static::CONDITION_NIN])) {
            $this->logger->warning(sprintf(
                'if_config NIN condition must be of type array; %s received',
                gettype($conditions[static::CONDITION_NIN])
            ), [
                'value' => $value,
                'conditions' => $conditions,
            ]);

            return false;
        }

        return !in_array($value, $conditions[static::CONDITION_NIN]);
    }

    /**
     * @param mixed $value
     * @param array $conditions
     * @return bool
     */
    private function evaluateEq($value, array $conditions)
    {
        if (!array_key_exists(static::CONDITION_EQ, $conditions)) {
            return true;
        }

        if (is_numeric($value) && is_numeric($conditions[static::CONDITION_EQ])) {
            return (float)$value === (float)$conditions[static::CONDITION_EQ];
        }

        return $value === $conditions[static::CONDITION_EQ];
    }

    /**
     * @param mixed $value
     * @param array $conditions
     * @return bool
     */
    private function evaluateNeq($value, array $conditions)
    {
        if (!array_key_exists(static::CONDITION_NEQ, $conditions)) {
            return true;
        }

        if (is_numeric($value) && is_numeric($conditions[static::CONDITION_NEQ])) {
            return (float)$value !== (float)$conditions[static::CONDITION_NEQ];
        }

        return $value !== $conditions[static::CONDITION_NEQ];
    }

    /**
     * @param mixed $value
     * @param array $conditions
     * @return bool
     */
    private function evaluateGt($value, array $conditions)
    {
        if (!array_key_exists(static::CONDITION_GT, $conditions)) {
            return true;
        }

        if (!is_numeric($value) || !is_numeric($conditions[static::CONDITION_GT])) {
            $this->logger->warning('if_config GT condition and value must be numeric', [
                'value' => $value,
                'conditions' => $conditions,
            ]);

            return false;
        }

        return $value > $conditions[static::CONDITION_GT];
    }

    /**
     * @param mixed $value
     * @param array $conditions
     * @return bool
     */
    private function evaluateLt($value, array $conditions)
    {
        if (!array_key_exists(static::CONDITION_LT, $conditions)) {
            return true;
        }

        if (!is_numeric($value) || !is_numeric($conditions[static::CONDITION_LT])) {
            $this->logger->warning('if_config LT condition and value must be numeric', [
                'value' => $value,
                'conditions' => $conditions,
            ]);

            return false;
        }

        return $value < $conditions[static::CONDITION_LT];
    }
}
