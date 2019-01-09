# About

This is Cms Magento 2 module for integration with smsp.by api service.
For more info look [this service site](https://smsp.by/).

# Install

* `composer require 'sablesoft/magento2-smsp'`
* `bin/magento module:enable SableSoft_Smsp`
* `bin/magento cache:clean`
* `bin/magento cache:flush`
* `bin/magento setup:upgrade`
* `bin/magento setup:di:compile`

# Use

* Set your smsp user and apikey in admin panel
* Use SableSoft\Smsp\Model\Api for api requesting