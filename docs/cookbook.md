# Custom Entity Bundle

## Installation :

```bash
composer --prefer-dist require akeneo-labs/custom-entity-bundle 2.0.*
```

Follow the bundle documentation : https://github.com/akeneo-labs/CustomEntityBundle/blob/master/README.md#installation

## Create the reference data

To create your entity, you have to create a class that extends AbstractCustomEntity. In this cookbook, we will create an entity called Supplier with only one property `name`.

AbstractCustomEntity already declares 2 mandatory properties : `code` and `id`, so you just have to add your entity specific properties.

```php
<?php

namespace Acme\Bundle\AppBundle\Entity;

use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;

class Supplier extends AbstractCustomEntity
{
    /**
    * @var string
     */
    protected $name;
    
    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }
}
```

Then, create the doctrine configuration for this entity to declare the entity fields and table name where the entity will be persisted.

```yaml
#Acme\Bundle\SupplierBundle\Resources\config\doctrine\Supplier.orm.yml
Acme\Bundle\SupplierBundle\Entity\Supplier:
    type: entity
    table: refdata_supplier
    repositoryClass: Pim\Bundle\CustomEntityBundle\Entity\Repository\CustomEntityRepository
    fields:
        id:
            type: integer
            id: true
            generator:
                strategy: AUTO
        code:
            type: string
            length: 25
            nullable: false
            unique: true
        name:
            type: string
            length: 255
            nullable: false
```

## Update the database

Each new reference data is persisted in a dedicated table. You can see what will be the create table SQL query by executing this command :
```bash
bin/console doctrine:schema:update --dump-sql
```

In our case, this will output something like this : 
```sql
CREATE TABLE refdata_supplier (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(25) NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_EF5B01F877153098 (code), PRIMARY KEY(id));
```

Now run the command by replacing the `--dump-sql` option by `--force` to create the table in the database :
```bash
bin/console doctrine:schema:update --force
```

You can check that the table is correctly created in your database.
 
## Your reference data as an attribute
Now declare the new reference data in the application : 
```yaml
#app/config/config.yml
pim_reference_data:
    supplier:
        class: Acme\Bundle\SupplierBundle\Entity\Supplier
        type: simple
```

To check every thing is OK so far, execute the following commands :
```bash
#check if the entity doctrine configuration is OK
bin/console doctrine:mapping:info
```
```bash
#check if the reference data is correct
bin/console pim:reference-data:check
```

At this point, the reference data can already be used as an attribute in your families. Try to create a new "reference data simple select" attribute.

## Reference data CRUD

### Menu
A new menu item can be added to access the reference data list. To achieve that, declare the menu item by creating a new configuration file :
```yaml
#Acme\Bundle/SupplierBundle/Resources/config/form_extensions/menu.yml
extensions:
    acme-menu-supplier:
        module: pim/menu/item
        parent: pim-menu-reference_data-navigation-block
        position: 10
        config:
            title: acme.menu.item.reference_data.supplier
            to: pim_customentity_index
            routeParams:
                customEntityName: supplier
```
At this point the item menu should be visible at the end of the menu. You may have to clear your cache (including the front end cache) to see it.

### Datagrid configuration

Create a new configuration file to declare the datagrid configuration for supplier. First, declare the source and columns configuration just to have a basic list page : 

```yaml
#Acme/Bundle/SupplierBundle/Resources/config/datagrid/supplier.yml
datagrid:
    supplier:
        options:
            entityHint: supplier
            manageFilters: false
        source:
            type: pim_datasource_default
            entity: Acme\Bundle\SupplierBundle\Entity\Supplier
            repository_method: createDatagridQueryBuilder
        columns:
            code:
                label: acme.supplier.field.code
            name:
                label: acme.supplier.field.name
```

#### Form extension

Declare the reference data index page form extension. Exemple of working configuration : https://github.com/akeneo-labs/CustomEntityBundle/blob/master/docs/examples/CustomBundle/Resources/config/form_extensions/brand/index.yml

Declare your new custom entity :
```yaml
#Acme/Bundle/SupplierBundle/Resources/config/custom_entities.yml
custom_entities:
    supplier:
        entity_class: Acme\Bundle\SupplierBundle\Entity\Supplier
```

At this point, you should have a basic empty list of reference data, with no sort ou filter available. A create button may be visible, but not configured yet.

#### Add filters on the list :

We can make the list filterable by adding the following configuration :

```yaml
#Acme/Bundle/SupplierBundle/Resources/config/datagrid/supplier.yml
datagrid:
    supplier:
        #[...]
        filters:
            columns:
                code:
                    type: string
                    label: acme.supplier.field.code
                    data_name: rd.code
                name:
                    type: search
                    label: acme.supplier.field.name
                    data_name: rd.name
```

You should now see a new text field to filter the list.

#### Make the list sortable :

```yaml
#Acme/Bundle/SupplierBundle/Resources/config/datagrid/supplier.yml
datagrid:
    supplier:
        #[...]
        sorters:
            columns:
                code:
                    data_name: rd.code
                name:
                    data_name: rd.name
            default:
                code: '%oro_datagrid.extension.orm_sorter.class%::DIRECTION_ASC'
```

The column headers should now be clickable to sort the results.

#### Add the creation form

At this point, the list page is ready to display some content. Let's add the creation form to be able to create new reference data. You can find a working example here : https://github.com/akeneo-labs/CustomEntityBundle/blob/master/docs/examples/CustomBundle/Resources/config/form_extensions/brand/create.yml

Once the configuration file is complete, try to create a new reference data. You should have an error, because once the creation is done, you are redirected to the edit form which do not exists yet. But if you go back to the list, your new reference data should be here.

#### Add the edition form

You can find a working example here : https://github.com/akeneo-labs/CustomEntityBundle/blob/master/docs/examples/CustomBundle/Resources/config/form_extensions/brand/edit.yml

If you try to click on a reference data to edit it, you'll see it doesn't work. It's because the edit link is not configured for the datagrid. Add the following configuration to declare the route and the edit button :

```yaml
#Acme/Bundle/SupplierBundle/Resources/config/datagrid/supplier.yml
datagrid:
    supplier:
        #[...]
        properties:
            id: ~
            edit_link:
                type: url
                route: pim_customentity_rest_get
                params:
                    - id
                    - customEntityName
        actions:
            edit:
                type:      navigate
                label:     acme.supplier.this.edit
                icon:      edit
                link:      edit_link
                rowAction: true
```

And add the following configuration to link the entity to its edition form :
```yaml
#Acme/Bundle/SupplierBundle/Resources/config/custom_entities.yml
custom_entities:
    supplier:
        options:
            edit_form_extension: acme-supplier-edit-form
```

At this point, you should be able to dislay the edition form. But you should also see that all the fields are empty except the code. It’s because the entity has no normalizer, so we need to write one :

```php
<?php

namespace Acme\Bundle\SupplierBundle\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\scalar;
use Acme\Bundle\SupplierBundle\Entity\Supplier;

class SupplierNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'id' => $object->getId(),
            'code' => $object->getCode(),
            'name' => $object->getName(),
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Supplier;
    }

}
```

The `normalize` method must return an array containing the reference data properties, with the property names as array keys and the property values as array values.

Now declare the normalizer as a service :

```yaml
#Acme/Bundle/SupplierBundle/Resources/config/services.yml
services:
    acme.normalizer.supplier:
        class: Acme\Bundle\SupplierBundle\Normalizer\SupplierNormalizer
        tags:
            - {name: pim_serializer.normalizer, priority: 200}
```

The service must be tagged as “pim_serializer.normalizer” to be identified as a serializer. You now have to load the 
services.yml file via the dependency injection :

```php
<?php

namespace Acme\Bundle\SupplierBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

class AcmeSupplierExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
```

At this point, you should be able to see all the fields of your reference data. If you click on the save button, the reference data should be saved and the list should also display the updated informations.

#### Add the deletion action

Same as the edition form, we need to declare the delete route and button

```yaml
#Acme/Bundle/SupplierBundle/Resources/config/datagrid/supplier.yml
datagrid:
    supplier:
        #[...]
        actions:
            delete:
                type: delete
                label: acme.supplier.this.delete
                icon: trash
                link: delete_link
```
```yaml
#Acme/Bundle/SupplierBundle/Resources/config/datagrid/supplier.yml
datagrid:
    supplier:
        #[...]
        properties:
            delete_link:
                type: url
                route: pim_customentity_rest_delete
                params:
                    - id
                    - customEntityName
```
You should now be able to delete any reference data by clicking the delete icon on the list.

At this point, the datagrid configuration is fully functionnal, and you should be able to list, edit or delete any reference data value.

## Internationalization

