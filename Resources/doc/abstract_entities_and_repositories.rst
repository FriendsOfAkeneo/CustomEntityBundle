Abstract Entites and Repositories
=================================

The bundle provides a series of abstract entities  :


Entity\\AbstractCustomEntity
----------------------------

This entity implements the basic interfaces that are used in Akeneo. It defines a "code" property which is
used throughout the PIM as a reference.

The repositories for this entity should extend
``Pim\Bundle\CustomEntityBundle\Entity\Repository\CustomEntityRepository``.


Entity\\AbstractTranslatableCustomEntity
----------------------------------------

This entity extends the AbstractCustomEntity, and provides a link to a translation entity.

The entity containg the translations should extend ``Pim\Bundle\TranslationBundle\Entity\AbstractTranslation``.

The repositories for this entity should extend
``Pim\Bundle\CustomEntityBundle\Entity\Repository\TranslatableCustomEntityRepository``.


Entity\\AbstractCustomOption
----------------------------

This entity should be used when a custom attribute should be linked the entity.

The repositories for this entity should extend
``Pim\Bundle\CustomEntityBundle\Entity\Repository\CustomOptionRepository``.


Entity\\AbstractTranslatableCustomOption
----------------------------------------

This entity should be used when a custom attribute should be linked the entity, and when some properties of the entity
have to be localized.

The entity containing the translations should extend
``Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomOptionTranslation``

The repositories for this entity should extend
``Pim\Bundle\CustomEntityBundle\Entity\Repository\TranslatableCustomOptionRepository``.
