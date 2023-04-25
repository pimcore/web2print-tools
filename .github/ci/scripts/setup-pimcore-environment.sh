#!/bin/bash

set -eu -o xtrace

cp .github/ci/files/.env .

if [ ${REQUIRE_ADMIN_BUNDLE} = true ]; then
    composer require -n --no-update pimcore/admin-ui-classic-bundle
fi