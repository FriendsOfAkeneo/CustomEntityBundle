<?php

namespace Pim\Bundle\CustomEntityBundle\MassAction;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\From;
use Doctrine\ORM\QueryBuilder;
use Oro\Bundle\DataGridBundle\Datagrid\Manager;
use Oro\Bundle\DataGridBundle\Datagrid\RequestParameters;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionParametersParser;
use Oro\Bundle\FilterBundle\Grid\Extension\OrmFilterExtension;
use Pim\Bundle\DataGridBundle\Datasource\Datasource;
use Symfony\Component\HttpFoundation\Request;

/**
 * Creates a query builder for a datagrid
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DataGridQueryGenerator
{
    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var RequestParameters
     */
    protected $requestParams;

    /**
     * @var MassActionParametersParser
     */
    protected $parametersParser;

    /**
     * Constructor
     *
     * @param Manager                    $manager
     * @param RequestParameters          $requestParams
     * @param MassActionParametersParser $parametersParser
     */
    public function __construct(Manager $manager, RequestParameters $requestParams, MassActionParametersParser $parametersParser)
    {
        $this->manager = $manager;
        $this->requestParams = $requestParams;
        $this->parametersParser = $parametersParser;
    }

    /**
     * Creates a query builder based on the grid
     *
     * @param Request $request
     * @param string  $datagridName
     *
     * @return QueryBuilder
     *
     * @throws \LogicException
     */
    public function createQueryBuilder(Request $request, $datagridName)
    {
        $parameters = $this->parametersParser->parse($request);

        if ($parameters['inset'] && empty($parameters['values'])) {
            throw new \LogicException(sprintf('This request is empty'));
        }

        $datagrid = $this->manager->getDatagrid($datagridName);
        $this->requestParams->set(OrmFilterExtension::FILTER_ROOT_PARAM, $parameters['filters']);

        $datasource = $datagrid->getDatasource();
        if (!$datasource instanceof Datasource) {
            throw new \LogicException("Mass actions applicable only for datagrids with ORM datasource.");
        }

        $qb = $datagrid->getAcceptedDatasource()->getQueryBuilder();
        if (count($parameters['values'])) {
            $valueWhereCondition =
                $parameters['inset']
                    ? $qb->expr()->in('o.id', $parameters['values'])
                    : $qb->expr()->notIn('o.id', $parameters['values']);
            $qb->andWhere($valueWhereCondition);
        }

        $qb->setMaxResults(null);
        $qb->setFirstResult(null);

        return $qb;
    }

    /**
     * @param Request $request
     * @param string $datagridName
     *
     * @return array
     */
    public function getIds(Request $request, $datagridName)
    {
        $qb = $this->createQueryBuilder($request, $datagridName);
        $qb->resetDQLPart('select');
        $qb->select('o.id');
        $qb->add('from', new From(current($qb->getRootEntities()), 'o'), false);
        $qb->groupBy('o.id');

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * Returns the count of selected objects
     *
     * @param Request $request
     * @param string  $datagridName
     *
     * @return int
     */
    public function getCount(Request $request, $datagridName)
    {
        $qb = $this->createQueryBuilder($request, $datagridName);
        $qb->resetDQLPart('select');
        $qb->select('COUNT(o.id)');
        $qb->add('from', new From(current($qb->getRootEntities()), 'o'), false);

        return $qb->getQuery()->getSingleScalarResult();
    }
}
