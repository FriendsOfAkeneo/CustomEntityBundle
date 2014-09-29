Defining CRUD interfaces
========================

CRUD Configuration
------------------

The configuration for the CRUD actions of your custom entities must be in a file named 'config/custom_entities.yml', 
located in an activated bundle. To have a full working CRUD for an entity, the following configuration could be used:


.. code-block:: yaml
   
    custom_entities:
        my_entity:
            extends: quick_create
            entity_class: Acme\Bundle\CatalogBundle\Entity\MyEntity
            options:
                form_type: my_form_type
                acl_prefix: my_acl_prefix
            actions:
                create:
                    form_options: { create: true }

The root level of the file contains the configuration for all your entities, indexed by alias. The alias will be used in the 
CRUD URLs, and later, for the datagrid configuration.

For each entity, the following options are available:

abstract
  Set to true if the definition is only meant to be extended
extends
  The alias of the extended configuration.
  The bundle offers three base configurations that can be extended: default, quick_create, and mass_actions
options
  The global configuration options for the current entity
actions
  The configuration for the enabled CRUD actions
entity_class
  The class of the entity, **required** if the configuration is not abstract.
  (Container parameters can be used in the class value)


Global Configuration Options
****************************

The following options can be used:

manager
  Alias of the CRUD object manager. Default is "default".
acl_prefix
  A prefix for all ACLs of the CRUD. If not set, no ACLs will be set.
acl_separator
  The separator between the ACL prefix and the ACL suffix. Default is "_"
form_type
  The default form type for form actions.
form_options
  The default form options for form actions.
form_template
  The default template for form actions.

Common Action Options
*********************

The following options are common for all actions:

service
  The id of the action service
enabled
  Set to false if the action should not be enabled. **WARNING : This option is not inherited**
route
  The route for the action
acl
  The ACL for the action
acl_suffix
  If the global ``acl_prefix`` option is provided, and no acl is provided for the action, the acl option
  will be set to <acl_prefix><acl_separator><acl_suffix>


Index Action Options
********************

By default, the index action uses the ``pim_custom_entity.action.index`` service with the following options:

.. code-block:: yaml
   
    custom_entities:
        my_entity:
            entity_class: Acme\Bundle\CatalogBundle\Entity\MyEntity
            actions:
                index:
                    service: pim_custom_entity.action.index
                    route: pim_customentity_index
                    quick_create: false
                    template: PimCustomEntityBundle:CustomEntity:index.html.twig
                    row_actions: ['edit', 'delete']
                    

template
  The template of the action
row_actions
  An array of action types available for each row on the grid
mass_actions
  An array of available mass action types
quick_create
   True if the create action should be displayed in a lightbox. *(Requires the use of the 
   **pim_custom_entity.action.quick_create** service for the create action)*
quick_create_action_type
   The action type for the quick create action


Create Action Options
*********************

By default, the create action uses the ``pim_custom_entity.action.create`` service with the following options:

.. code-block:: yaml
   
    custom_entities:
        my_entity:
            entity_class: Acme\Bundle\CatalogBundle\Entity\MyEntity
            actions:
                create:
                    service: pim_custom_entity.action.create
                    route: pim_customentity_create
                    template: PimCustomEntityBundle:CustomEntity:form.html.twig
                    form_type: ~
                    form_options: {}
                    redirect_route: pim_customentity_index
                    redirect_route_parameters: { customEntityName: my_entity }
                    successs_message: flash.my_entity.created
                    create_values: {}
                    create_options: {}
                    save_options: {}
                     
                     
template
  The template of the action
form_type
   The form type used to create objects. **This option is required**
form_options
   Options which should be passed to the form factory
redirect_route
   The route to use for redirections on success
redirect_route_parameters
   The parameters for the redirect route
success_message
   A message which should be displayed on success
create_values
   An array of default properties for the created object
create_options
   An array of options which should be passed to the object manager for object creation
save_options
   An array of options which should be passed to the object manager for object saving


Edit Action Options
*******************

By default, the edit action uses the ``pim_custom_entity.action.edit`` service with the following options:

.. code-block:: yaml
   
    custom_entities:
        my_entity:
            entity_class: Acme\Bundle\CatalogBundle\Entity\MyEntity
            actions:
                edit:
                    service: pim_custom_entity.action.edit
                    route: pim_customentity_edit
                    template: PimCustomEntityBundle:CustomEntity:form.html.twig
                    form_type: ~
                    form_options: {}
                    redirect_route: pim_customentity_index
                    redirect_route_parameters: { customEntityName: my_entity }
                    successs_message: flash.my_entity.updated
                    grid_action_options:
                        type: navigate
                        label: Edit
                        icon: edit
                        link: edit_link
                        rowAction: true
                    save_options: {}
   
template
  The template of the action
form_type
   The form type used to create objects. **This option is required**
form_options
   Options which should be passed to the form factory
redirect_route
   The route to use for redirections on success
redirect_route_parameters
   The parameters for the redirect route
success_message
   A message which should be displayed on success
grid_action_options:
   An array of options for the Oro grid action
save_options:
   An array of options which should be passed to the object manager for object saving

Mass Edit Action Options
************************

By default, the mass edit action uses the ``pim_custom_entity.action.mass_edit`` service with the following options:

.. code-block:: yaml
   
    custom_entities:
        my_entity:
            entity_class: Acme\Bundle\CatalogBundle\Entity\MyEntity
            actions:
                mass_edit:
                    service: pim_custom_entity.action.mass_edit
                    route: pim_customentity_massedit
                    template: PimCustomEntityBundle:CustomEntity:massEdit.html.twig
                    form_type: ~
                    form_options: {}
                    redirect_route: pim_customentity_index
                    redirect_route_parameters: { customEntityName: my_entity }
                    successs_message: flash.my_entity.mass_edited
                    grid_action_options:
                        type: redirect
                        label: Mass Edit
                        icon: edit
                     
                     
template
  The template of the action
form_type
   The form type used to create objects. **This option is required**
form_options
   Options which should be passed to the form factory
redirect_route
   The route to use for redirections on success
redirect_route_parameters
   The parameters for the redirect route
success_message
   A message which should be displayed on success
grid_action_options:
   An array of options for the Oro grid action


Delete Action Options
*********************

By default, the delete action uses the ``pim_custom_entity.action.delete`` service with the following options:

.. code-block:: yaml
   
    custom_entities:
        my_entity:
            entity_class: Acme\Bundle\CatalogBundle\Entity\MyEntity
            actions:
                delete:
                    service: pim_custom_entity.action.delete
                    route: pim_customentity_delete
                    grid_action_options: 
                        type: delete
                        label: Delete
                        icon: trash

grid_action_options:
  An array of options for the Oro grid action


Mass Delete Action Options
**************************

By default, the mass delete action uses the ``pim_custom_entity.action.mass_delete`` service with the following options:

.. code-block:: yaml
   
    custom_entities:
        my_entity:
            entity_class: Acme\Bundle\CatalogBundle\Entity\MyEntity
            actions:
                index:
                    service: pim_custom_entity.action.delete
                    route: ~
                    grid_action_options: 
                        type: delete
                        label: Delete
                        entity_name: my_entity
                        data_identifier: o
                        launcherOptions: { icon: trash }


grid_action_options:
  An array of options for the Oro grid action


Datagrid Configuration
----------------------

The bundle will automatically add your configured actions to your oro datagrids if your datagrid extends the 
``custom_entity`` model. An example for a translatable option entity is available in the 
`examples folder <../examples/datagrid.yml>`_.
