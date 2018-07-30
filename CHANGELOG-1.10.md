# 1.10.9

## Bug fixes

- Remove deprecated frontend form validation; ensure compatibility with PIM ~1.7.26

## Beware

As of PIM v1.7.26, `JsFormValidationBundle` is no longer used in the PIM. If you still want to use front-end validation for your Custom Entity forms,
you can do so by requiring [fp/jsformvalidator-bundle](https://github.com/formapro/JsFormValidatorBundle) in your composer.json

# 1.10.8

## Improvements

Allow `Pim\Bundle\CustomEntityBundle\Updater\Updater` to cache several class metadata at the same time

# 1.10.7

## Bug fixes
Fix mass deletion reference data

# 1.10.5

## Bug fixes
- CEB-53: Do not remove custom entity used in one or more products 

# 1.10.4

## Bug fixes
- Fix mass delete: add a custom mass delete handler 

# 1.10.2

## Bug fixes
- CEB-48: Fix EE permissions on job profiles
- CEB-49: Fix Reference data field on job profiles

1.10.1
-----

## Improvements
- Reference data repository is API-ready                                                                         
