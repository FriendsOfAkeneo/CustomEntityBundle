# CustomEntityBundle

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/akeneo-labs/CustomEntityBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/akeneo-labs/CustomEntityBundle/?branch=master)
[![Build Status](https://travis-ci.org/akeneo-labs/CustomEntityBundle.svg?branch=master)](https://travis-ci.org/akeneo-labs/CustomEntityBundle)

Facilitates the creation of PIM reference data and related views in the PIM.

For more information, please see http://docs.akeneo.com/

## Requirements

| CustomEntityBundle   | Akeneo PIM Community Edition |
|:--------------------:|:----------------------------:|
| v2.0.*               | v2.0.*                       |
| v1.10.*              | v1.7.*                       |
| v1.9.*               | v1.6.*                       |
| v1.8.*               | v1.6.*                       |
| v1.7.*               | v1.5.*                       |

## Installation
You can install this bundle with composer (see requirements section):

```bash
    php composer.phar require "akeneo-labs/custom-entity-bundle":"2.0.*"
```

Then add the following lines **at the end** of your app/config/routing.yml :

```yaml
    pim_customentity:
        prefix: /reference-data
        resource: "@PimCustomEntityBundle/Resources/config/routing.yml"
```

and enable the bundle in the `app/AppKernel.php` file in the `registerBundles()` method:

```php
    $bundles = [
        // ...
        new Pim\Bundle\CustomEntityBundle\PimCustomEntityBundle(),
    ]
```

If your installation is already set up, you have to run the following command:

```bash
    php bin/console akeneo:batch:create-job "Akeneo Mass Edit Connector" "csv_reference_data_quick_export" "quick_export" "csv_reference_data_quick_export" '{"delimiter": ";", "enclosure": "\"", "withHeader": true, "filePath": "/tmp/reference_data_quick_export.csv"}'
```

## Documentation

The reference data documentation can be found in the 
[PIM documentation](https://docs.akeneo.com/2.0/manipulate_pim_data/catalog_structure/creating_a_reference_data.html).

The custom entity bundle provides the following tools to help managing custom entities inside the Akeneo PIM.

* [Abstract entities and repositories](docs/abstract_entities_and_repositories.md)
* [CRUD interface](docs/crud_interface.md)
* [CRUD managers](docs/crud_managers.md)

A demo project has been created [here](docs/examples/CustomBundle) to give more examples about what we can do.
It can easily installed using [this setup script](docs/examples/bin/setup_example.bash).

More info about configuration can be found in the [bundle documentation](docs/index.md).

## Contributing

If you want to contribute to this open-source project,
thank you to read and sign the following [contributor agreement](http://www.akeneo.com/contributor-license-agreement/)
