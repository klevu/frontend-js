<?php

namespace Klevu\FrontendJs;

class Constants
{
    const XML_PATH_FRONTENDJS_ENABLED = 'klevu_frontendjs/configuration/enabled';

    const LOCAL_STORAGE_KEY = 'klv_mage';
    const LOCAL_STORAGE_CUSTOMER_DATA_KEY = 'customerData';
    const LOCAL_STORAGE_TTL_CUSTOMER_DATA = 600;

    const COOKIE_KEY = 'klv_mage';
    const COOKIE_EXPIRE_SECTIONS_KEY = 'expire_sections';
    const DEFAULT_COOKIE_LIFETIME = 86400;
    const DEFAULT_COOKIE_PATH = '/';

    const JS_EVENTNAME_CUSTOMER_DATA_LOADED = 'klevu.customerData.loaded';
    const JS_EVENTNAME_CUSTOMER_DATA_LOAD_ERROR = 'klevu.customerData.loadError';
}
