#!/usr/bin/env bash

# Store useful pathes
MY_PATH=$(dirname $0)            # relative
MY_PATH=$( cd $MY_PATH && pwd )  # absolutized and normalized
if [ -z "$MY_PATH" ] ; then
  exit 1
fi
cd $MY_PATH/../CustomBundle
CUSTOM_BUNDLE_PATH=$(pwd)
echo $CUSTOM_BUNDLE_PATH
cd $MY_PATH/../../../../../../config
CONFIG_PATH=$(pwd)

# Configure routing
if grep -q PimCustomEntityBundle $CONFIG_PATH/routes/routes.yml
then
    echo "routing already exists"
else
    echo "routing will be configured"
    echo "" >> $CONFIG_PATH/routes/routes.yml
    cat $MY_PATH/config/routes.yml >> $CONFIG_PATH/routes/routes.yml
fi

if test -f $CONFIG_PATH/packages/akeneo_pim_structure.yml
then
  echo "reference data already configured"
else
  echo "reference data will be configured"
  cp $MY_PATH/config/akeneo_pim_structure.yml $CONFIG_PATH/packages/akeneo_pim_structure.yml
fi

# Customize Kernel
if grep -q AcmeCustomBundle $CONFIG_PATH/bundles.php
then
    echo "Kernel already configured"
else
    echo "Kernel will be configured"
fi
