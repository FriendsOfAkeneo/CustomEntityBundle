<?php

namespace Pim\Bundle\CustomEntityBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Pim\Bundle\CatalogBundle\Doctrine\ORM\Repository\AttributeRepository as BaseRepository;
use Pim\Bundle\CatalogBundle\Entity\Attribute;
use Pim\Component\Catalog\AttributeTypes;

/**
 * Factory for custom attribute repository
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AttributeRepositoryFactory
{
    /** @var EntityManagerInterface */
    protected $em;

    /**
     * AttributeRepositoryFactory constructor.
     *
     * @param EntityManagerInterface $em,
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return AttributeRepository
     */
    public function getCustomAttributesRepository()
    {
        $metadata = $this->em->getClassMetadata(Attribute::class);
        return new AttributeRepository($this->em, $metadata);
    }
}
