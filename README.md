# CustomEntityBundle

The CustomEntityBundle eases the creation of custom entities and related views in the [Akeneo PIM](https://github.com/akeneo/pim-community-standard).

## Installation
To install this bundle, please include `akeneo/custom-entity-bundle` to your `composer.json` :

    composer require akeneo/custom-entity-bundle

Add the following lines **at the very end** of your `app/config/routing.yml` :

    pim_customentity:
        prefix: /enrich
        resource: "@PimCustomEntityBundle/Resources/config/routing.yml"

## Resources
- [Cookbook](http://docs.akeneo.com/master/cookbook/custom_entity/index.html)
- [Technical documentation](Resources/doc/index.rst)
- [PIM Documentation](http://docs.akeneo.com)
