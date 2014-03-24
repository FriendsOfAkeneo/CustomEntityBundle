<?php

namespace Pim\Bundle\CustomEntityBundle\EventListener;

use Oro\Bundle\DataGridBundle\Event\BuildBefore;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;

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
     * @var Registry
     */
    protected $configurationRegistry;

    /**
     * Constructor
     *
     * @param Registry $configurationRegistry
     */
    public function __construct(Registry $configurationRegistry)
    {
        $this->configurationRegistry = $configurationRegistry;
    }

    /**
     * Check whenever grid is flexible and add flexible columns dynamically
     *
     * @param BuildBefore $event
     *
     * @throws \LogicException
     */
    public function buildBefore(BuildBefore $event)
    {
        $datagridConfig = $event->getConfig();
        if ('custom_entity' !== $datagridConfig->offsetGetByPath('[extends]')) {
            return;
        }

        $customEntityConfig = $this->configurationRegistry->get($datagridConfig->getName());
        $datagridConfig->offsetSetByPath(
            '[source]',
            [
                'entity' => $customEntityConfig->getEntityClass(),
                'type'   => 'pim_custom_entity'
            ]
        );
        $properties = ['id' => []];
        $actions = [];
        if ($customEntityConfig->hasAction('edit')) {
            $properties['edit_link'] = [
                'type'   => 'custom_entity_url',
                'route'  => sprintf('%s/edit', $customEntityConfig->getName()),
                'params' => ['id']
            ];
            $actions['edit'] = [
                'type'      => 'navigate',
                'label'     => 'Edit',
                'icon'      => 'edit',
                'link'      => 'edit_link',
                'rowAction' => true
            ];
        }
        if ($customEntityConfig->hasAction('remove')) {
            $properties['delete_link'] = [
                'type'   => 'custom_entity_url',
                'route'  => sprintf('%s/remove', $customEntityConfig->getName()),
                'params' => ['id']
            ];
            $actions['delete'] = [
                'type'      => 'delete',
                'label'     => 'Delete',
                'icon'      => 'trash',
                'link'      => 'delete_link'
            ];
        }
        $datagridConfig->offsetSetByPath('[properties]', $properties);
        $datagridConfig->offsetSetByPath('[actions]', $actions);
    }
}
