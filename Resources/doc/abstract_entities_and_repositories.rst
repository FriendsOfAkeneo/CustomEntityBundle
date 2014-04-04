Abstract Entites and Repositories
=================================

The bundle provides a series of abstract entities  :

Entity\AbstractCustomEntity
----------------------------

This entity implements the basic interfaces that are used in Akeneo. It defines a "code" property which is
used throughout the PIM as a reference.

The repositories for this entity should extend
``Pim\Bundle\CustomEntityBundle\Entity\Repository\AbstractCustomEntityRepository``.