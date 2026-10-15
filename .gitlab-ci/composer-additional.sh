#!/bin/bash
composer config repositories.hyva-themes/magento2-theme-module github https://github.com/hyva-themes/magento2-theme-module.git
composer config repositories.hyva-themes/magento2-default-theme github https://github.com/hyva-themes/magento2-default-theme.git
composer config repositories.hyva-themes/magento2-default-theme-csp github https://github.com/hyva-themes/magento2-default-theme-csp.git
composer config repositories.hyva-themes/magento2-base-layout-reset github https://github.com/hyva-themes/magento2-base-layout-reset.git
composer config repositories.hyva-themes/magento2-compat-module-fallback github https://github.com/hyva-themes/magento2-compat-module-fallback.git
composer config repositories.hyva-themes/magento2-mollie-theme-bundle github https://github.com/hyva-themes/magento2-mollie-theme-bundle.git
composer config repositories.hyva-themes/magento2-luma-checkout github https://github.com/hyva-themes/magento2-luma-checkout.git
composer config repositories.hyva-themes/magento2-email-module github https://github.com/hyva-themes/magento2-email-module.git
composer config repositories.hyva-themes/magento2-theme-fallback github https://github.com/hyva-themes/magento2-theme-fallback.git
composer config repositories.hyva-themes/magento2-order-cancellation-webapi github https://github.com/hyva-themes/magento2-order-cancellation-webapi.git

if [ "$CI_JOB_NAME" = "playwright" ]; then
    composer require -n -- hyva-themes/magento2-default-theme-csp hyva-themes/magento2-theme-module:^1.5.3
fi

composer config --global --auth --list
