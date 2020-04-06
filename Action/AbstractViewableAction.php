<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Event\ActionEventManager;
use Pim\Bundle\CustomEntityBundle\Manager\Registry as ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractViewableAction extends AbstractAction
{
    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @param ActionFactory       $actionFactory
     * @param ActionEventManager  $eventManager
     * @param ManagerRegistry     $managerRegistry
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     * @param Environment     $twig
     */
    public function __construct(
        ActionFactory $actionFactory,
        ActionEventManager $eventManager,
        ManagerRegistry $managerRegistry,
        RouterInterface $router,
        TranslatorInterface $translator,
        Environment $twig
    ) {
        parent::__construct($actionFactory, $eventManager, $managerRegistry, $router, $translator);

        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public function doExecute(Request $request)
    {
        return $this->renderResponse(
            [
                'object' => $this->findEntity($request)
            ]
        );
    }

    /**
     * @param array $templateVars
     *
     * @return Response
     */
    public function renderResponse(array $templateVars = [])
    {
        list($template, $templateVars) = $this->eventManager->dispatchPreRenderEvent(
            $this,
            $this->options['template'],
            $templateVars + $this->getDefaultTemplateVars()
        );

        return $this->twig->render($template, $templateVars);
    }

    /**
     * Returns the default template vars
     *
     * @return array
     */
    protected function getDefaultTemplateVars()
    {
        $vars = [
            'customEntityName' => $this->configuration->getName(),
            'baseTemplate'     => $this->options['base_template']
        ];

        if ($this->configuration->hasAction('index')) {
            $vars['indexUrl'] = $this->getActionUrl('index');
        }

        return $vars;
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultOptions(OptionsResolver $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setRequired(['template']);
        $resolver->setDefaults(['base_template' => 'PimEnrichBundle::layout.html.twig']);
    }
}
