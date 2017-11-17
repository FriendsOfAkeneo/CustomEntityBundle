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

### Custom components

#### Custom Entity dropdown list for single and multi select

You can use a form component to link one custom entity to another one.
One example is available in the provided AcmeCustomBundle.

Custom entity that supports single select:

```yaml
acme_custom-edit-form-properties-association:
    module: custom_entity/field/custom-entity-select
    parent: acme_custom-edit-form-properties-common
    targetZone: content
    position: 270
    config:
        fieldName: fabric
        choiceNameField: code
        choiceValueField: name
        isCustomEntity: true
        entityName: fabric
        required: false
```

Custom entity that supports multi select:

```yaml
acme_custom-edit-form-properties-association:
    module: custom_entity/field/custom-entity-select
    parent: acme_custom-edit-form-properties-common
    targetZone: content
    position: 270
    config:
        fieldName: fabric
        choiceNameField: code
        choiceValueField: name
        isCustomEntity: true
        entityName: fabric
        required: false
        isMultiple: true
```

Field on a custom entity that refers to a built in akeneo type (special usecase for us)

```yaml
pim-entity-edit-form-properties-attribute:
    module: custom_entity/field/custom-entity-select
    parent: pim-entity-edit-form-properties-common
    targetZone: content
    position: 270
    config:
        fieldName: attribute
        choiceNameField: code
        choiceValueField: name
        isCustomEntity: false
        entityName: attribute
        required: false
```

## Internals

### Normalizers

All your custom entities must implement a standard normalizer defining the standard format returned by the REST API calls.

Most of the time, it will be a simple flat array of the entity properties.

Again, you will find examples in the [Custom Bundle](examples/CustomBundle/Normalizer).

This custom normalizer is used in the internal [CustomEntity normalizer](../Normalizer/CustomEntityNormalizer.php).

### Validation

Entity validation is configured with a YAML file in the standard Symfony way.

Example: [Color validation](examples/CustomBundle/Resources/config/validation.yml).

## AcmeCustomBundle

This extension comes with an extended example bundle that you can use to see Custom Entities in action.

### Installation

Register the bundle in AppKernel:

```php
    $bundles = [
        // ...
        new \Pim\Bundle\CustomEntityBundle\PimCustomEntityBundle(),
        new \Acme\Bundle\CustomBundle\AcmeCustomBundle(),
    ]
```

Update your Doctrine schema:

```bash
    php ./bin/console doctrine:schema:update --dump-sql
    php ./bin/console doctrine:schema:update --force
```

Do not forget to clear your cache before using the application.
