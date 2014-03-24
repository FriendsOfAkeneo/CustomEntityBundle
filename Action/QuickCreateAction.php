<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

/**
 * Quick create action
 * 
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class QuickCreateAction extends CreateAction
{
    /**
     * {@inheritdoc}
     */
    protected function getRedirectResponse(array $options)
    {
        $response = array(
            'status' => 1,
            'url' => $this->getRedirectPath($options)
        );

        return new Response(json_encode($response));
    }
}
