# Akeneo Custom Entity Bundle

The custom entity bundle provides the following tools to help managing custom entities inside the Akeneo PIM.

* [Abstract entities and repositories](abstract_entities_and_repositories.md)
* [CRUD interface](crud_interface.md)
* [CRUD managers](crud_managers.mb)

## User Interface

### Navigation

To add your custom entity in the `Reference data` menu, you must add it to the `menu.yml` form extension.

You will find an example of these form extensions in the example
[Custom Bundle](examples/CustomBundle/Resources/config/form_extensions/menu.yml).


### CRUD form

The custom entity CRUD form are defined as form extension. 
You will find corresponding examples in the 
[Custom Bundle](examples/CustomBundle/Resources/config/form_extensions/color).

* `index.yml`: defines the custom entity grid.
The grid must also be configured as all other PIM grids.
Here is the example for the [Color entity](examples/CustomBundle/Resources/config/datagrid/color.yml).

* `create.yml`: defines the creation form. In this form you will declare all the entity fields.

* `edit.yml`: defines the edition form.

The delete modal window does not need to be defined because it's part of the core PIM.

## Internals

### Normalizers

All your custom entities must implement a standard normalizer defining the standard format returned by the REST API calls.

Most of the time, it will be a simple flat array of the entity properties.

Again, you will find examples in the [Custom Bundle](examples/CustomBundle/Normalizer).

This custom normalizer is used in the internal [CustomEntity normalizer](../Normalizer/CustomEntityNormalizer.php).

### Validation

Entity validation is configured with a YAML file in the standard Symfony way.

Example: [Color validation](examples/CustomBundle/Resources/config/validation.yml).
