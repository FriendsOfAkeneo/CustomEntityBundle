1.6.0
-----

## New feature
- Compatibility with Akeneo PIM 1.4.x
- Compliant with Akeneo PIM Reference data feature
- Travis integration
- Create generic reference data form type `Pim\Bundle\CustomEntityBundle\Form\Type\CustomEntityType`
- Add default validation on code for reference datas

## Improvements
- Better integration with Scrutinizer
- Quick export is launch in backend to be compliant with Akeneo PIM 1.4.x
- Mass Delete uses Akeneo PIM 1.4 feature instead of custom one
- Update templates to be compliant with Akeneo PIM 1.4.x
- Remove abstraction in phpspec tests
- Code should not be editable on the UI

## Bug fixes
- Fix `Pim\Bundle\CustomEntityBundle\Action\MassEditAction` constructor to use `Pim\Bundle\CustomEntityBundle\Manager\ManagerRegistry` instead of `Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface`
- Keep url parameters on Mass Edit actions
- Fix accessible QuickCreate url from browser. We redirect if not an XML HTTP Request
- Fix Get a 500 when creating a reference data with an already existing code

## BC Breaks
- Replace deprecated `Symfony\Component\OptionsResolver\OptionsResolverInterface` by `Symfony\Component\OptionsResolver\OptionsResolver` everywhere it was used

- Remove `PimCustomEntityBundle::layout.html.twig`. The default configuration uses `PimEnrichBundle::layout.html.twig` now
- Remove `Pim\Bundle\CustomEntityBundle\Action\GridActionInterface`
- Remove `Pim\Bundle\CustomEntityBundle\Action\MassDeleteAction` to use the PIM one
- Remove `Pim\Bundle\CustomEntityBundle\MassAction\DataGridQueryGenerator` class and use `Oro\Bundle\PimDataGridBundle\Extension\MassAction\MassActionDispatcher` instead
- Remove `Pim\Bundle\CustomEntityBundle\AttributeType\CustomOptionSimpleSelectType` and `Pim\Bundle\CustomEntityBundle\AttributeType\CustomOptionMultiSelectType` classes
- Remove `Pim\Bundle\CustomEntityBundle\Datasource\CustomEntityDatasource`
- Remove `Pim\Bundle\CustomEntityBundle\Doctrine\ORM\Sorter\CodeOptionSorter`, `Pim\Bundle\CustomEntityBundle\Doctrine\ORM\Sorter\LabelOptionSorter` and `Pim\Bundle\CustomEntityBundle\Doctrine\ORM\Sorter\TranslatableOptionSorter` classes
- Remove `Pim\Bundle\CustomEntityBundle\Entity\Repository\DatagridAwareRepositoryInterface`
- Remove `Pim\Bundle\CustomEntityBundle\Entity\Repository\LocaleAwareRepositoryInterface`
- Remove `Pim\Bundle\CustomEntityBundle\EventListener\DataGrid\ConfigureCustomEntityGridListener`
- Remove `Pim\Bundle\CustomEntityBundle\Extension\Formatter\Property\ProductValue\MultipleCustomOptionProperty`, `Pim\Bundle\CustomEntityBundle\Extension\Formatter\Property\ProductValue\MultipleTranslatableCustomOptionProperty`, `Pim\Bundle\CustomEntityBundle\Extension\Formatter\Property\ProductValue\SimpleCustomOptionProperty` and `Pim\Bundle\CustomEntityBundle\Extension\Formatter\Property\ProductValue\SimpleTranslatableCustomOptionProperty`
- Remove `Pim\Bundle\CustomEntityBundle\Extension\Formatter\Property\UrlProperty`
- Remove `Pim\Bundle\CustomEntityBundle\Form\Extension\ClearMissingExtension`, `Pim\Bundle\CustomEntityBundle\Form\Subscriber\ClearMissingSubscriber` and `Pim\Bundle\CustomEntityBundle\Form\Subscriber\NullValue`
- Remove `Pim\Bundle\CustomEntityBundle\Normalizer\MongoDBReferableNormalizer`

- Inject `Oro\Bundle\PimDataGridBundle\Extension\MassAction\MassActionDispatcher` in `Pim\Bundle\CustomEntityBundle\Action\MassEditAction` instead of `Pim\Bundle\CustomEntityBundle\MassAction\DataGridQueryGenerator`
- Inject `Oro\Bundle\PimDataGridBundle\Extension\MassAction\MassActionDispatcher`, `Akeneo\Platform\Bundle\ImportExportBundle\Entity\Repository\JobInstanceRepository`, `Akeneo\Tool\Bundle\BatchBundle\Launcher\JobLauncherInterface` and `Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface` in `Pim\Bundle\CustomEntityBundle\Action\QuickExportAction` of `Symfony\Bridge\Doctrine\RegistryInterface`, `Pim\Bundle\CustomEntityBundle\MassAction\DataGridQueryGenerator` and `Symfony\Component\Serializer\Serializer`
- Repositories don't extend `Akeneo\Pim\Enrichment\Bundle\Doctrine\ORM\QueryBuilderUtilityDoctrine\ReferableEntityRepository` or implement `Akeneo\Platform\Bundle\UIBundle\Entity\Repository\OptionRepositoryInterface` anymore.
- Methods `getOption`, `getOptionId`, `getOptionLabel` and `getOptions` have been removed from repositories
- `Pim\Bundle\CustomEntityBundle\Entity\Repository\TranslatableCustomEntityRepository` implements neither `Pim\Bundle\CustomEntityBundle\Entity\Repository\DatagridAwareRepositoryInterface` nor `Pim\Bundle\CustomEntityBundle\Entity\Repository\LocaleAwareRepositoryInterface`
- Methods `createDatagridQueryBuilder` and `setLocale` and instance variable `locale` have been removed from `Pim\Bundle\CustomEntityBundle\Entity\Repository\TranslatableCustomEntityRepository`

- Change visibility of `Pim\Bundle\CustomEntityBundle\Factory\ActionFactory::$actions` from private to protected
- Add abstract method `getCustomEntityName()` in `Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity`. This method should return the configuration name of your custom entity

- Remove `grid_action_options` from `custom_entities.yml` configuration files

## Removed features
- Custom attribute types has been removed (PIM feature)
- Custom entity formatters has been removed (PIM feature)
- URL Property formatter has been removed (should not be done here)
