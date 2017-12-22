# CustomEntityBundle

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/akeneo-labs/CustomEntityBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/akeneo-labs/CustomEntityBundle/?branch=master)
[![Build Status](https://travis-ci.org/akeneo-labs/CustomEntityBundle.svg?branch=master)](https://travis-ci.org/akeneo-labs/CustomEntityBundle)

Facilitates the creation of PIM reference data and related views in the PIM.

For more information, please see http://docs.akeneo.com/

## Requirements

| CustomEntityBundle   | Akeneo PIM Community Edition |
|:--------------------:|:----------------------------:|
| v2.1.*               | v2.1.*                       |
| v2.0.*               | v2.0.*                       |
| v1.10.*              | v1.7.*                       |
| v1.9.*               | v1.6.*                       |
| v1.8.*               | v1.6.*                       |
| v1.7.*               | v1.5.*                       |

## Installation
You can install this bundle with composer (see requirements section):

```bash
    php composer.phar require "akeneo-labs/custom-entity-bundle":"2.1.*"
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
        new \Pim\Bundle\CustomEntityBundle\PimCustomEntityBundle(),
    ]
```

If your installation is already set up, you have to run the following commands:

* First one to add the mass edit job:
 
```bash
    php bin/console akeneo:batch:create-job "Akeneo Mass Edit Connector" "csv_reference_data_quick_export" "quick_export" "csv_reference_data_quick_export" '{"delimiter": ";", "enclosure": "\"", "withHeader": true, "filePath": "/tmp/reference_data_quick_export.csv"}'
```

## Documentation

The reference data documentation can be found in the 
[PIM documentation](https://docs.akeneo.com/2.0/manipulate_pim_data/catalog_structure/creating_a_reference_data.html).

Detailled information can be found in the [bundle documentation](docs/index.md).

## Contributing

If you want to contribute to this open-source project,
thank you to read and sign the following [contributor agreement](http://www.akeneo.com/contributor-license-agreement/)
