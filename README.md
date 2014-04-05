CustomEntityBundle
==================

Eases the creation of custom entity and related views in the PIM

For more information, please see http://docs.akeneo.com/

To install this bundle, please include akeneo/custom-entity-bundle to your composer.json 
and add the following lines **at-the-end** of your app/config/routing.yml :

    pim_customentity:
        prefix: /enrich
        resource: "@PimCustomEntityBundle/Resources/config/routing.yml"

Some example usages for this bundle can be found in the [PIM documentation][http://docs.akeneo.com/master/cookbook/custom_entity/index.html]

The full documentation for the bundle can be found in the [Resources/doc][Resources/doc/index.rst] folder.
