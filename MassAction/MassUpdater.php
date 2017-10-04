<?php

namespace Pim\Bundle\CustomEntityBundle\MassAction;

use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Mass action updater
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MassUpdater
{
    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    /**
     * Constructor
     *
     * @param RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Mass updates entities
     *
     * @param string $class
     * @param array  $data
     * @param array  $ids
     */
    public function updateEntities(string $class, array $data, array $ids): void
    {
        $qb = $this->doctrine->getManager()->createQueryBuilder()
            ->update($class, 'o')
            ->where('o.id IN (:ids)')
            ->setParameter('ids', $ids);
        foreach ($data as $key => $value) {
            $qb->set("o.$key", ":$key")->setParameter(":$key", $value);
        }

        $qb->getQuery()->execute();
    }
}
