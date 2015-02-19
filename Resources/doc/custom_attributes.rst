Custom Attributes
=================

To create an attribute based on a custom entity, you will have to override the PIM ProductValue entity to add your own
backend type, as described in the 
`PIM documentation <http://docs.akeneo.com/master/cookbook/custom_entity/creating_an_attribute_type.html>`_.

This bundle provides two classes to help you quickly create multiple and single select types based on a custom entity.


Single Option Attribute
-----------------------

A single option attribute can be configured in the following way:

.. code-block:: yaml
   
    parameters:
        pim_datagrid.product.attribute_type.my_attribute_type:
            column:
                type:        product_value_option
                selector:    product_value_option
            filter:
                type:        product_value_choice
                parent_type: ajax_choice
                options:
                    field_options:
                        multiple: true
            sorter:          product_value
    services:
        acme_catalog.attributetype.color:
            class: '%pim_custom_entity.attribute_type.custom_option_simple_select.class%'
            arguments:
                - my_backend_type
                - pim_ajax_entity
                - '@pim_catalog.validator.attribute_constraint_guesser'
                - my_attribute_type
                - Acme\Bundle\CatalogBundle\Entity\Color
            tags:
                - { name: pim_catalog.attribute_type, alias: my_attribute_type, entity: '%pim_catalog.entity.product.class%' }
                
Please note the following:

* The configuration parameter for your attribute type **must** start with ``pim_datagrid.product.attribute_type.`` and end
  with the alias of your attribute type
* The first parameter of the service must be the backend type the attribute is linked to
* The fourth parameter of the service must be the alias of your attribute type.

Multiple Option Attribute
-------------------------

A multiple options attribute can be configured in the following way:

.. code-block:: yaml
   
    parameters:
        pim_datagrid.product.attribute_type.my_attribute_type:
            column:
                type:        product_value_options
                selector:    product_value_options
            filter:
                type:        product_value_choice
                parent_type: ajax_choice
                options:
                    field_options:
                        multiple: true
            sorter:          product_value
    services:
        acme_catalog.attributetype.color:
            class: '%pim_custom_entity.attribute_type.custom_option_multi_select.class%'
            arguments:
                - my_backend_type
                - pim_ajax_entity
                - '@pim_catalog.validator.attribute_constraint_guesser'
                - my_attribute_type
                - Acme\Bundle\CatalogBundle\Entity\Color
            tags:
                - { name: pim_catalog.attribute_type, alias: my_attribute_type, entity: '%pim_catalog.entity.product.class%' }
                
Please note the following:

* The configuration parameter for your attribute type **must** start with ``pim_datagrid.product.attribute_type.`` and end
  with the alias of your attribute type
* The first parameter of the service must be the backend type the attribute is linked to
* The fourth parameter of the service must be the alias of your attribute type.
