<?php

namespace Klevu\FrontendJs\Model;

use Klevu\FrontendJs\Api\Data\CustomerDataInterface;

class CustomerData implements CustomerDataInterface
{
    const REVALIDATE_AFTER_SECONDS = 1800;

    /**
     * @var string
     */
    private $sessionId = '';

    /**
     * @var int|null
     */
    private $customerGroupId;

    /**
     * @var string|null
     */
    private $idCode;

    /**
     * @var string
     */
    private $shopperIp = '';

    /**
     * @var int
     */
    private $revalidateAfter;

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param string $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @return int|null
     */
    public function getCustomerGroupId()
    {
        return $this->customerGroupId;
    }

    /**
     * @param int|null $customerGroupId
     */
    public function setCustomerGroupId($customerGroupId)
    {
        $this->customerGroupId = $customerGroupId;
    }

    /**
     * @return string|null
     */
    public function getIdCode()
    {
        return $this->idCode;
    }

    /**
     * @param string|null $idCode
     */
    public function setIdCode($idCode)
    {
        $this->idCode = $idCode;
    }

    /**
     * @return string
     */
    public function getShopperIp()
    {
        return $this->shopperIp;
    }

    /**
     * @param string $shopperIp
     */
    public function setShopperIp($shopperIp)
    {
        $this->shopperIp = $shopperIp;
    }

    /**
     * @return int
     */
    public function getRevalidateAfter()
    {
        if (null === $this->revalidateAfter) {
            $this->setRevalidateAfter(time() + static::REVALIDATE_AFTER_SECONDS);
        }

        return $this->revalidateAfter;
    }

    /**
     * @param int $revalidateAfter
     * @return void
     */
    public function setRevalidateAfter($revalidateAfter)
    {
        $this->revalidateAfter = (int)$revalidateAfter;
    }
}
