#!/bin/bash

server/tools/php-cs-fixer/vendor/bin/php-cs-fixer fix server/src
git add .