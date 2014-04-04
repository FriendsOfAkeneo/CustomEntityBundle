CustomEntityBundle
==================

Eases the creation of custom entity and related views in the PIM

For more information, please see http://docs.akeneo.com/

To install this bundle, please include akeneo/custom-entity-bundle to your composer.json 
and add the following lines *at-the-end* to your app/config/routing.yml :

    pim_customentity:
        prefix: /enrich
        resource: "@PimCustomEntityBundle/Resources/config/routing.yml"
