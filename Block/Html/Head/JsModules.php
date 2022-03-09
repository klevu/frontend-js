<?php

namespace Klevu\FrontendJs\Block\Html\Head;

use Klevu\FrontendJs\Block\Template as FrontendJsTemplate;
use Klevu\FrontendJs\Constants;
use Magento\Customer\Model\Group as CustomerGroup;

/**
 * @todo Use ViewModels when older Magento BC support dropped
 */
class JsModules extends FrontendJsTemplate
{
    /**
     * @return string
     */
    public function getLocalStorageKey()
    {
        return Constants::LOCAL_STORAGE_KEY;
    }

    /**
     * @return string
     */
    public function getCustomerDataKey()
    {
        return Constants::LOCAL_STORAGE_CUSTOMER_DATA_KEY;
    }

    /**
     * @return int
     */
    public function getNotLoggedInCustomerGroupId()
    {
        return CustomerGroup::CUST_GROUP_ALL;
    }
}
