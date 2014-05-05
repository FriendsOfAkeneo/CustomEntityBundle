<?php

namespace Pim\Bundle\CustomEntityBundle\Datasource\Orm;

use Doctrine\ORM\EntityManager;
use Oro\Bundle\DataGridBundle\Datagrid\DatagridInterface;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Pim\Bundle\CatalogBundle\Helper\LocaleHelper;
use Pim\Bundle\CustomEntityBundle\Entity\Repository\DatagridAwareRepositoryInterface;
use Pim\Bundle\CustomEntityBundle\Entity\Repository\LocaleAwareRepositoryInterface;
use Pim\Bundle\DataGridBundle\Datasource\Orm\OrmDatasource;
use Pim\Bundle\DataGridBundle\Datasource\ResultRecord\HydratorInterface;

/**
 * Datasource for custom entity datagrids
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CustomEntityDatasource extends OrmDatasource
{
    /**
     * @var string
     */
    const TYPE = 'pim_custom_entity';

    /**
     * @var LocaleHelper
     */
    protected $localeHelper;

    /**
     * Constructor
     *
     * @param EntityManager     $em
     * @param AclHelper         $aclHelper
     * @param HydratorInterface $hydrator
     * @param LocaleHelper      $localeHelper
     */
    public function __construct(
        EntityManager $em,
        AclHelper $aclHelper,
        HydratorInterface $hydrator,
        LocaleHelper $localeHelper
    ) {
        parent::__construct($em, $aclHelper, $hydrator);
        $this->localeHelper = $localeHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function process(DatagridInterface $grid, array $config)
    {
        $repository = $this->em->getRepository($config['entity']);

        if ($repository instanceof LocaleAwareRepositoryInterface) {
            $repository->setLocale($this->localeHelper->getCurrentLocale()->getCode());
        }

        if (!isset($config['repository_method']) && $repository instanceof DatagridAwareRepositoryInterface) {
            $config['repository_method'] = 'createDatagridQueryBuilder';
        }
        parent::process($grid, $config);
    }
}
