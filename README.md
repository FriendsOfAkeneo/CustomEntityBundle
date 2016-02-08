# CustomEntityBundle

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/akeneo/CustomEntityBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/akeneo/CustomEntityBundle/?branch=master)
[![Build Status](https://travis-ci.org/akeneo-labs/CustomEntityBundle.svg?branch=master)](https://travis-ci.org/akeneo-labs/CustomEntityBundle)

Facilitates the creation of PIM reference data and related views in the PIM.

For more information, please see http://docs.akeneo.com/

## Requirements

| CustomEntityBundle   | Akeneo PIM Community Edition |
|:--------------------:|:----------------------------:|
| v1.6.*               | v1.4.*                       |
| v1.5.0-RC1           | v1.3.*                       |
| v1.4.*               | v1.2.*                       |
| v1.3.*               | v1.2.*                       |
| v1.2.*               | v1.1.*                       |
| v1.1.*               | v1.1.*                       |

## Installation
You can install this bundle with composer (see requirements section):

    $ php composer.phar require akeneo-labs/custom-entity-bundle:1.6.*

Then add the following lines **at the end** of your app/config/routing.yml :

    pim_customentity:
        prefix: /reference-data
        resource: "@PimCustomEntityBundle/Resources/config/routing.yml"

and enable the bundle in the `app/AppKernel.php` file in the `registerBundles()` method:

    $bundles = [
        // ...
        new Pim\Bundle\CustomEntityBundle\PimCustomEntityBundle(),
    ]

If you want to use the quick export and/or the mass edit features, you have to load the job fixture defined in  [Resources/fixtures/jobs.yml](Resources/fixtures/jobs.yml) file and to copy/paste its content to your installer.

If your installation is already set up, you have to run the following command:

    php app/console akeneo:batch:create-job "Akeneo Mass Edit Connector" "reference_data_quick_export" "quick_export" "csv_reference_data_quick_export" '{"delimiter": ";", "enclosure": "\\", "withHeader": true, "filePath": "/tmp/reference_data_quick_export.csv"}'


## Documentation

Some example usages for this bundle can be found in the [PIM documentation](http://docs.akeneo.com/master/cookbook/custom_entity/index.html)

The custom entity bundle provides the following tools to help managing custom entities inside the Akeneo PIM.

* [Abstract entities and repositories](Resources/doc/abstract_entities_and_repositories.rst)
* [CRUD interface](Resources/doc/crud_interface.rst)
* [CRUD managers](Resources/doc/crud_managers.rst)

The cookbook section is only available for Akeneo PIM 1.3 (and previous versions): http://docs.akeneo.com/latest/cookbook/custom_entity/index.html
We will create some for Akeneo PIM 1.4 as soon as possible.

## Contributing

If you want to contribute to this open-source project, thank you to read and sign the following [contributor agreement](http://www.akeneo.com/contributor-license-agreement/)
