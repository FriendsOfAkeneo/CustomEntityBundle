#Custom Entity Bundle

##Installation :

```bash
composer --prefer-dist require akeneo-labs/custom-entity-bundle 2.0.*
```

Follow the bundle documentation : https://github.com/akeneo-labs/CustomEntityBundle/blob/master/README.md#installation

##Create the reference data

Create a new entity, for example "Supplier" with only one property `name`, and extend AbstractCustomEntity. AbstractCustomEntity already declares 2 mandatory properties : `code` and `id`, so you just have to add your entity specific properties.
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

Then, create the doctrine configuration for this entity to declare the entity fields and the table name where the entity will be persisted.

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

##Update the database

Each new reference data are persisted in a dedicated table. You can see what will be the query to create the table by executing this command :
```bash
bin/console doctrine:schema:update --dump-sql
```

Now run the command by replacing the `--dump-sql` option by `--force` to create the table in the database :
```bash
bin/console doctrine:schema:update --force
```

You can check that the table is correctly created in your database.
 
##Your reference data as an attribute
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

##Reference data CRUD

###Menu
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

###Datagrid configuration
Create a file to declare the datagrid configuration for supplier. First, declare the source and columns configuration : 
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
Then, declare the edit and delete route configurations for your reference data in the datagrid :

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
            delete_link:
                type: url
                route: pim_customentity_rest_delete
                params:
                    - id
                    - customEntityName
```
Now configure the row actions :
```yaml
#Acme/Bundle/SupplierBundle/Resources/config/datagrid/supplier.yml
datagrid:
    supplier:
        #[...]
        actions:
            edit:
                type: navigate
                label: acme.supplier.this.edit
                icon: edit
                link: edit_link
                rowAction: true
            delete:
                type: delete
                label: acme.supplier.this.delete
                icon: trash
                link: delete_link
```
Add the filters configuration :
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
And finally add the sorters configuration :
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

###Form extensions
Declare the reference data index page form extension :

TODO: Pour contenu lien vers form_extensions/brand/index.yml
```yaml
#Acme/Bundle/SupplierBundle/Resources/config/form_extensions/supplier/index.yml
extensions:
    pim-supplier-index:
        module: pim/common/simple-view
        config:
            template: pim/template/common/default-template
        forwarded-events:
            grid_load:start: grid_load:start
            grid_load:complete: grid_load:complete

    acme-supplier-index-grid-container:
        module: pim/common/simple-view
        parent: pim-supplier-index
        targetZone: content
        config:
            template: pim/template/form/index/index

    acme-supplier-index-user-navigation:
        module: pim/menu/user-navigation
        parent: pim-supplier-index
        targetZone: user-menu
        config:
            userAccount: pim_menu.user.user_account
            logout: pim_menu.user.logout

    acme-supplier-index-grid-title:
        module: pim/common/grid-title
        parent: pim-supplier-index
        targetZone: title
        config:
            title: acme.supplier.index_title

    acme-supplier-index-breadcrumbs:
        module: pim/common/breadcrumbs
        parent: pim-supplier-index
        targetZone: breadcrumbs
        config:
            tab: pim-menu-reference_data
            item: acme-menu-supplier

    acme-supplier-index-grid-filters-list:
        module: oro/datafilter/filters-list
        parent: acme-supplier-index-grid-container
        targetZone: filters

    acme-supplier-index-grid-filters-manage:
        module: oro/datafilter/filters-button
        parent: acme-supplier-index-grid-container
        targetZone: filters

    acme-supplier-index-pagination:
        module: oro/datagrid/pagination-input
        parent: acme-supplier-index-grid-container
        targetZone: toolbar
        config:
            gridName: supplier

    acme-supplier-index-grid:
        module: pim/form/common/index/grid
        parent: pim-supplier-index
        targetZone: content
        position: 1000
        config:
            alias: supplier

    acme-supplier-index-create-button:
        module: pim/form/common/index/create-button
        parent: pim-supplier-index
        targetZone: buttons
        config:
           title:     pim_custom_entity.button.create
           modalForm: acme-supplier-create-modal
```

Add the create page form extension :
```yaml
#Acme/Bundle/SupplierBundle/Resources/config/datagrid/create.yml
extensions:
    acme-supplier-create-modal:
        module: custom_entity/form/creation/modal
        config:
            labels:
               title: pim_custom_entity.create_popin.title
               subTitle: acme.menu.item.reference_data.supplier
            successMessage: acme.supplier.messages.created.success
            editRoute: pim_customentity_rest_get
            picture: illustrations/Family.svg
            routerKey: code
            postUrl:
                route: pim_customentity_rest_create
                parameters:
                    customEntityName: supplier

    acme-supplier-create-code:
        module: pim/form/common/creation/field
        parent: acme-supplier-create-modal
        targetZone: fields
        position: 10
        config:
            identifier: code
            label: acme.supplier.field.code

    acme-supplier-create-name:
        module: pim/form/common/creation/field
        parent: acme-supplier-create-modal
        targetZone: fields
        position: 20
        config:
            identifier: name
            label: acme.supplier.field.name
```
And the edition page form extension :
```yaml
#Acme/Bundle/SupplierBundle/Resources/config/datagrid/edit.yml
extensions:
    acme-supplier-edit-form:
        module: pim/form/common/edit-form

    acme-supplier-edit-form-breadcrumbs:
        module: pim/common/breadcrumbs
        parent: acme-supplier-edit-form
        targetZone: breadcrumbs
        config:
            tab: pim-menu-reference_data
            item: acme-menu-supplier

    acme-supplier-edit-form-cache-invalidator:
        module: pim/cache-invalidator
        parent: acme-supplier-edit-form
        position: 1000

    acme-supplier-edit-form-secondary-actions:
        module: pim/form/common/secondary-actions
        parent: acme-supplier-edit-form
        targetZone: buttons
        position: 50

    acme-supplier-edit-form-delete:
        module: custom_entity/form/common/delete
        parent: acme-supplier-edit-form-secondary-actions
        targetZone: secondary-actions
        position: 100
        config:
            route: pim_customentity_rest_delete
            routeParams:
                customEntityName: supplier
            trans:
                title: acme.supplier.messages.remove.confirm
                container: pim_enrich.confirmation.delete_item
                success: acme.supplier.messages.remove.success
                fail: acme.supplier.messages.remove.fail
            redirect: pim_customentity_index

    acme-supplier-edit-form-save-buttons:
        module: pim/form/common/save-buttons
        parent: acme-supplier-edit-form
        targetZone: buttons
        position: 120

    acme-supplier-edit-form-state:
        module: pim/form/common/state
        parent: acme-supplier-edit-form
        targetZone: state
        position: 900
        config:
            entity: pim_enrich.entity.group.title

    acme-supplier-edit-form-save:
        module: custom_entity/form/common/save-form
        parent: acme-supplier-edit-form
        targetZone: buttons
        position: 0
        config:
            updateSuccessMessage: acme.supplier.messages.edit.success
            updateFailureMessage: acme.supplier.messages.edit.fail
            url: pim_customentity_rest_edit
            route_params:
                customEntityName: supplier
            redirectAfter: pim_customentity_rest_get
            excludedProperties: []

    acme-supplier-edit-form-form-tabs:
        module: pim/form/common/form-tabs
        parent: acme-supplier-edit-form
        targetZone: content
        position: 100

    acme-supplier-edit-form-properties-tab:
        module: pim/common/tab
        parent: acme-supplier-edit-form-form-tabs
        targetZone: container
        position: 100
        config:
            label: 'pim_custom_entity.form.tab.properties.title'

    acme-supplier-edit-form-properties-sections:
        module: pim/common/simple-view
        parent: acme-supplier-edit-form-properties-tab
        targetZone: self
        config:
            template: pim/template/form/tab/sections

    acme-supplier-edit-form-section-code:
        module: pim/common/simple-view
        parent: acme-supplier-edit-form-properties-sections
        targetZone: accordion
        position: 80
        config:
            template: pim/template/form/tab/section
            templateParams:
                sectionTitle: acme.supplier.section.code
                dropZone: content

    acme-supplier-edit-form-properties-code:
        module: pim/form/common/fields/text
        parent: acme-supplier-edit-form-section-code
        targetZone: content
        position: 90
        config:
            fieldName: code
            required: true
            readOnly: true

    acme-supplier-edit-form-section-other:
        module: pim/common/simple-view
        parent: acme-supplier-edit-form-properties-sections
        targetZone: accordion
        position: 100
        config:
            template: pim/template/form/tab/section
            templateParams:
                sectionTitle: acme.supplier.section.other
                dropZone: content

    acme-supplier-edit-form-properties-name:
        module: pim/form/common/fields/text
        parent: acme-supplier-edit-form-section-other
        targetZone: content
        position: 90
        config:
            fieldName: name
            required: true
```

The last step for the datagrid configuration is to link the reference data to the correct edition form :
```yaml
#Acme/Bundle/SupplierBundle/Resources/config/custom_entities.yml
custom_entities:
    supplier:
        entity_class: Acme\Bundle\SupplierBundle\Entity\Supplier
        options:
            edit_form_extension: acme-supplier-edit-form
```
TODO: essayer de supprimer les optiosn et déplacer ça avant la création de la grille
TODO: afficher la grille en plusieurs étapes. colonnes puis filtres, puis tris, puis actions
TODO: Premiere action = delete
TODO: Seconde action = edit qui doit permettre de ccréer le form extension edit 

At this point, the datagrid is stating to work. You can see en empty list of reference data and create a new one.

##Normalizer
If we try to create then edit a reference data, the edit form is empty. It’s because the entity has no normalizer, so we need to write one :
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
The service must be tagged as “pim_serializer.normalizer” to be identified as a serializer. You now have to load the services.yml file via the dependency injection :
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

At this point, the datagrid configuration is fully functionnal, and you should be able to list, edit or delete any reference data value. You may have to clear your cache (back and front) to make it work.

##internationalization

