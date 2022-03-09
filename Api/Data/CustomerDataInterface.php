<?php

namespace Klevu\FrontendJs\Api\Data;

interface CustomerDataInterface
{
    /**
     * @return string
     */
    public function getSessionId();

    /**
     * @param string $sessionId
     * @return void
     */
    public function setSessionId($sessionId);

    /**
     * @return int|null
     */
    public function getCustomerGroupId();

    /**
     * @param int $customerGroupId
     * @return void
     */
    public function setCustomerGroupId($customerGroupId);

    /**
     * @return string|null
     */
    public function getIdCode();

    /**
     * @param string $idCode
     * @return void
     */
    public function setIdCode($idCode);

    /**
     * @return string
     */
    public function getShopperIp();

    /**
     * @param string $shopperIp
     * @return void
     */
    public function setShopperIp($shopperIp);

    /**
     * @return int
     */
    public function getRevalidateAfter();

    /**
     * @param int $revalidateAfter
     * @return void
     */
    public function setRevalidateAfter($revalidateAfter);
}
