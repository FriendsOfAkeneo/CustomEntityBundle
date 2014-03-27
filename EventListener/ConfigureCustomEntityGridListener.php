<?php

namespace Pim\Bundle\CustomEntityBundle\EventListener;

use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Oro\Bundle\DataGridBundle\Event\BuildBefore;
use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;

/**
 * Automatically configures pim_custom_entity grids
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ConfigureCustomEntityGridListener
{
    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * Constructor
     *
     * @param ActionFactory $actionFactory
     */
    public function __construct(ActionFactory $actionFactory)
    {
        $this->actionFactory = $actionFactory;
    }

    /**
     * Check whenever grid is flexible and add flexible columns dynamically
     *
     * @param BuildBefore $event
     *
     * @throws \Exception
     */
    public function buildBefore(BuildBefore $event)
    {
        $datagridConfig = $event->getConfig();
        if ('custom_entity' !== $datagridConfig->offsetGetByPath('[extends]')) {
            return;
        }

        $indexAction = $this->actionFactory->getAction($datagridConfig->getName(), 'index');
        if (!$indexAction) {
            throw new \Exception(sprintf('No index action configured for %s', $datagridConfig->getName()));
        }

        $this->setSource($datagridConfig, $indexAction);
        $this->setRowActions($datagridConfig, $indexAction);
        $this->setMassActions($datagridConfig, $indexAction);
    }

    /**
     * Sets the source in the config
     * 
     * @param \Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration $datagridConfig
     * @param \Pim\Bundle\CustomEntityBundle\Action\ActionInterface $indexAction
     */
    protected function setSource(DatagridConfiguration $datagridConfig, ActionInterface $indexAction)
    {
        $customEntityConfig = $indexAction->getConfiguration();
        $datagridConfig->offsetSetByPath(
            '[source]',
            [
                'entity' => $customEntityConfig->getEntityClass(),
                'type'   => 'pim_custom_entity'
            ]
        );
    }

    /**
     * Sets the configuration for row actions
     * 
     * @param DatagridConfiguration $datagridConfig
     * @param ConfigurationInterface $customEntityConfig
     */
    protected function setRowActions(DatagridConfiguration $datagridConfig, ActionInterface $indexAction)
    {
        $name = $indexAction->getConfiguration()->getName();
        $properties = ($datagridConfig->offsetGetByPath('[properties]') ?: []) + ['id' => []];
        $actions = $datagridConfig->offsetGetByPath('[actions]') ?: [];

        foreach ($indexAction->getRowActions() as $rowActionType) {
            if (isset($actions[$rowActionType])) {
                continue;
            }

            $link = $rowActionType . '_link';
            $rowAction  = $this->actionFactory->getAction($name, $rowActionType);
            $actions[$rowActionType] = $rowAction->getGridActionOptions() + ['link' => $link];
            $properties[$link] = [
                'type'   => 'custom_entity_url',
                'route'  => sprintf('%s/%s', $name, $rowActionType),
                'params' => ['id']
            ];
        }

        $datagridConfig->offsetSetByPath('[actions]', $actions);
        $datagridConfig->offsetSetByPath('[properties]', $properties);
    }

    /**
     * Sets the mass actions
     * 
     * @param \Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration $datagridConfig
     * @param \Pim\Bundle\CustomEntityBundle\Action\ActionInterface $indexAction
     */
    protected function setMassActions(DatagridConfiguration $datagridConfig, ActionInterface $indexAction)
    {
        $name = $indexAction->getConfiguration()->getName();
        $massActions = $datagridConfig->offsetGetByPath('[mass_actions]') ?: [];

        foreach ($indexAction->getMassActions() as $massActionType) {
            if (isset($massActions[$massActionType])) {
                continue;
            }

            $massAction = $this->actionFactory->getAction($name, $massActionType);
            $massActions[$massAction->getType()] = $massAction->getGridActionOptions();
        }

        $datagridConfig->offsetSetByPath('[mass_actions]', $massActions);
    }
}
