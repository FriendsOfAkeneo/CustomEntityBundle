# Defining CRUD interfaces

## CRUD Configuration

The configuration for the CRUD actions of your custom entities must be in a file named 'config/custom_entities.yml',
located in an activated bundle. To have a full working CRUD for an entity, the following configuration could be used:

```yaml
    # Resources/config/custom_entities.yml

    custom_entities:
        color:
            entity_class: Acme\Bundle\CustomBundle\Entity\Color
            options:
                acl_prefix: acme_enrich_color
            actions:
                edit:
                    form_type: acme_enrich_color
                create:
                    form_type: acme_enrich_color
                mass_edit:
                    form_type: acme_enrich_mass_edit_color
                quick_export:
                    service: pim_custom_entity.action.quick_export
```

The root level of the file contains the configuration for all your entities, indexed by alias. The alias will be used in the
CRUD URLs, and later, for the datagrid configuration.

For each entity, the following options are available:

- **abstract**: set to `true` if the definition is only meant to be extended
- **extends**: the alias of the extended configuration. The bundle offers three base configurations that can be extended: default, quick_create, and mass_actions
- **options**: general options for the CRUD
- **actions**: the configuration for the enabled CRUD actions
- **entity_class**: the class of the entity, **required** if the configuration is not abstract.
  (Container parameters can be used in the class value)


## Global Configuration Options

The following options can be used:

- **manager**: alias of the CRUD object manager. Default is "default".
- **acl_prefix**: a prefix for all ACLs of the CRUD. If not set, no ACLs will be set.
- **acl_separator**: the separator between the ACL prefix and the ACL suffix. Default is "_"

## Common Action Options

The following options are common for all actions:

- **service**: the id of the action service
- **enabled**: set to false if the action should not be enabled. **WARNING : This option is not inherited**
- **acl**: the ACL for the action
- **acl_suffix**: if the global ``acl_prefix`` option is provided, and no acl is provided for the action, the acl - **option** will be set to <acl_prefix><acl_separator><acl_suffix>


## Datagrid Configuration

The bundle will automatically add your configured actions to your oro datagrids if your datagrid extends the `custom_entity` model.
An example for a translatable option entity is available in the 
[example bundle](examples/CustomBundle).
