Defining CRUD interfaces
========================

CRUD Configuration
------------------

The configuration for the CRUD actions of your custom entities must be in a file named 'config/custom_entities.yml', 
situated in an activated bundle. To have a full working CRUD for an entity, the following configuration could be used :


.. code-block:: yaml
   
    custom_entities:
        my_entity:
            extends: quick_create
            class: Acme\Bundle\CatalogBundle\Entity\MyEntity
            actions:
                create:
                    form_type: my_form_type
                edit:
                    form_type: my_form_type

The root level of the file contains the configuration for all your entities, indexed by alias. The alias will be used in the 
CRUD URLs, and later, for the datagrid configuration.

For each entity, the following options are available :

abstract
  Set to true if the definition is only meant to be extended
extends
  The alias of the extended configuration.
  The bundle propose three base configurations that can be extended : defalut, quick_create, and mass_actions
actions
  The configuration for the enabled CRUD actions
class
  The class of the entity, **required** if the configuration is not abstract.
  (Container parameters can be used in the class value)
   

Common Action Options
*********************

The following options are common for all actions :

service
  The id of the action service
enabled
  Set to false if the action should not be enabled. **WARNING : This option is not inherited**


Index Action Options
********************

Datagrid Configuration
----------------------
