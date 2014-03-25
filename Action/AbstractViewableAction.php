<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Abstract viewable action
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractViewableAction extends AbstractAction
{
    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * Constructor
     *
     * @param ActionFactory       $actionFactory
     * @param ManagerInterface    $manager
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     * @param EngineInterface     $templating
     */
    public function __construct(
        ActionFactory $actionFactory,
        ManagerInterface $manager,
        RouterInterface $router,
        TranslatorInterface $translator,
        EngineInterface $templating
    ) {
        parent::__construct($actionFactory, $manager, $router, $translator);
        $this->templating = $templating;
    }

    /**
     * Renders a response
     *
     * @param array $templateVars
     *
     * @return Response
     */
    public function renderResponse(array $templateVars = [])
    {
        return $this->templating->renderResponse(
            $this->options['template'],
            $templateVars + $this->getDefaultTemplateVars()
        );
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
    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setRequired(['template']);
        $resolver->setDefaults(['base_template' => 'PimCustomEntityBundle::layout.html.twig']);
    }
}
