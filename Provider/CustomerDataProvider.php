<?php

namespace Klevu\FrontendJs\Provider;

use Klevu\FrontendJs\Api\CustomerDataProviderInterface;
use Klevu\FrontendJs\Api\Data\CustomerDataInterface;
use Klevu\FrontendJs\Api\Data\CustomerDataInterfaceFactory;
use Klevu\FrontendJs\Api\SessionIdProviderInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Session\SessionManagerInterface;
use Psr\Log\LoggerInterface;

class CustomerDataProvider implements CustomerDataProviderInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SessionManagerInterface
     */
    private $customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var SessionIdProviderInterface
     */
    private $sessionIdProvider;

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var CustomerDataInterfaceFactory
     */
    private $customerDataFactory;

    /**
     * @var CustomerInterface
     */
    private $customer;

    public function __construct(
        LoggerInterface $logger,
        SessionManagerInterface $customerSession,
        CustomerRepositoryInterface $customerRepository,
        SessionIdProviderInterface $sessionIdProvider,
        RemoteAddress $remoteAddress,
        CustomerDataInterfaceFactory $customerDataFactory
    ) {
        $this->logger = $logger;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->sessionIdProvider = $sessionIdProvider;
        $this->remoteAddress = $remoteAddress;
        $this->customerDataFactory = $customerDataFactory;
    }

    /**
     * @return CustomerDataInterface
     */
    public function execute()
    {
        /** @var CustomerDataInterface $customerData */
        $customerData = $this->customerDataFactory->create();
        $customerData->setSessionId($this->sessionIdProvider->execute());
        $customerData->setShopperIp($this->remoteAddress->getRemoteAddress(false));

        $customer = $this->getCustomer();
        if ($customer) {
            $customerData->setCustomerGroupId((int)$customer->getGroupId());
            $customerData->setIdCode($this->generateIdCode($customer));
        }

        return $customerData;
    }

    /**
     * @return CustomerInterface|null
     */
    private function getCustomer()
    {
        if (null === $this->customer && method_exists($this->customerSession, 'getCustomerId')) {
            $customerId = (int)$this->customerSession->getCustomerId();
            try {
                $this->customer = $this->customerRepository->getById($customerId);
            } catch (NoSuchEntityException $e) {
                // No action required
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), ['customerId' => $customerId]);
            }
        }

        return $this->customer;
    }

    /**
     * @param CustomerInterface $customer
     * @return string
     */
    private function generateIdCode(CustomerInterface $customer)
    {
        // @todo Replace with more secure hash mechanism
        return sprintf('enc-%s', md5($customer->getEmail()));
    }
}
