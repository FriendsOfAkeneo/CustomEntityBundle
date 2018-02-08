# 2.2.0
## Bug fixes
- Forbid reference data removal when used by a product
- Rework export to deal with simple reference data, reference data with translations, reference data linked to other reference data

## BC Breaks
- Remove `Normalizer\ReferableNormalizer`, `pim_custom_entity.normalizer.referable.class` parameter and `pim_custom_entity.normalizer.referable` service
- Remove `Checker\ProductLinkChecker`, `Checker\ProductLinkCheckerInterface`, `pim_custom_entity.checker.product_link.class` parameter and `pim_custom_entity.checker.product_link` service
- Remove `Remover\CustomEntityRemover`
- Move `Repository\AttributeRepository` to `Entity\Repository\AttributeRepository`

- Change `__constructor` of `Connector/Processor/Normalization/ReferenceDataProcessor` to remove `PropertyAccessorInterface` parameter

