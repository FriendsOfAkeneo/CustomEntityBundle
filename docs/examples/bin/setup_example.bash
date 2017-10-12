#!/usr/bin/env bash

# Store useful pathes
MY_PATH=$(dirname $0)            # relative
MY_PATH=$( cd $MY_PATH && pwd )  # absolutized and normalized
if [ -z "$MY_PATH" ] ; then
  exit 1
fi
cd $MY_PATH/../Acme/Bundle/CustomBundle
CUSTOM_BUNDLE_PATH=$(pwd)
echo $CUSTOM_BUNDLE_PATH
cd $MY_PATH/../../../../../../../../../app
APP_PATH=$(pwd)

# Configure routing
if grep -q PimCustomEntityBundle $APP_PATH/config/routing.yml
then
    echo "routing already exists"
else
    echo "routing will be configured"
    echo "" >> $APP_PATH/config/routing.yml
    cat $MY_PATH/config/routing.yml >> $APP_PATH/config/routing.yml
fi

# Configure Reference data
if grep -q "pim_reference_data: ~" $APP_PATH/config/config.yml
then
    echo "Reference data will be configured"
    sed -i '/pim_reference_data: ~/d' $APP_PATH/config/config.yml
    cat $MY_PATH/config/config-reference-data.yml >> $APP_PATH/config/config.yml
fi

if grep -q CustomBundle $APP_PATH/config/config.yml
then
    echo "Reference data already configured"
else
    echo "Reference data will be configured"
    echo "" >> $APP_PATH/config/config.yml
    NEW_CONFIG=$(cat $MY_PATH/config/config-reference-data.yml)
    NEW_CONFIG_ESCAPED=${NEW_CONFIG//$'\n'/\\$'\n'}
    sed -i "s/pim_reference_data:/${NEW_CONFIG_ESCAPED}/g" $APP_PATH/config/config.yml
fi

# Customize AppKernel
if grep -q AcmeCustomBundle $APP_PATH/AppKernel.php
then
    echo "AppKernel already configured"
else
    echo "AppKernel will be configured"
fi

