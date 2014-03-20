<?php

namespace Pim\Bundle\CustomEntityBundle\Datasource\Orm;

use Doctrine\ORM\EntityManager;
use Exception;
use Oro\Bundle\DataGridBundle\Datagrid\DatagridInterface;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Pim\Bundle\CatalogBundle\Helper\LocaleHelper;
use Pim\Bundle\CustomEntityBundle\Entity\Repository\DatagridAwareRepositoryInterface;
use Pim\Bundle\CustomEntityBundle\Entity\Repository\LocaleAwareRepositoryInterface;
use Pim\Bundle\DataGridBundle\Datasource\Orm\OrmDatasource;

/**
 * Description of CustomEntityDatasource
 *
 * @author Antoine Guigan <aguigan@qimnet.com>
 */
class CustomEntityDatasource extends OrmDatasource
{
    /**
     * @var string
     */
    const TYPE = 'pim_custom_entity';

    /**
     *
     * @var LocaleHelper
     */
    protected $localeHelper;

    /**
     * Constructor
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper $aclHelper
     * @param \Pim\Bundle\CatalogBundle\Helper\LocaleHelper $localeHelper
     */
    public function __construct(EntityManager $em, AclHelper $aclHelper, LocaleHelper $localeHelper)
    {
        parent::__construct($em, $aclHelper);
        $this->localeHelper = $localeHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function process(DatagridInterface $grid, array $config)
    {
        if (!isset($config['entity'])) {
            throw new Exception(get_class($this).' expects to be configured with entity');
        }

        $repository = $this->em->getRepository($config['entity']);

        if ($repository instanceof LocaleAwareRepositoryInterface) {
            $repository->setLocale($this->localeHelper->getCurrentLocale());
        }

        if (!isset($config['repository_method']) && $repository instanceof DatagridAwareRepositoryInterface) {
            $config['repository_method'] = 'createDatagridQueryBuilder';
        }
        parent::process($grid, $config);
    }
}
