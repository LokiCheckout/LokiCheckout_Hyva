#!/bin/bash
composer config repositories.private-packagist composer https://hyva-themes.repo.packagist.com/yireo-com/
composer config --auth http-basic.hyva-themes.repo.packagist.com token $HYVA_TOKEN

