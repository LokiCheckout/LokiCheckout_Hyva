#!/bin/bash
if [ "$GITHUB_JOB" = "integration-tests" ] && [ ! -f /tmp/magento/app/etc/config.php ]; then
  php -d memory_limit=-1 /tmp/magento/bin/magento module:enable --all
  php -r '$file = "/tmp/magento/app/etc/config.php"; $config = include $file; $config["modules"][getenv("MAGENTO_MODULE")] = 1; file_put_contents($file, "<?php\nreturn " . var_export($config, true) . ";\n");'
fi
