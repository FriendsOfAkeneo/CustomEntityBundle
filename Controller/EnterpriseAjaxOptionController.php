<?php

namespace Pim\Bundle\CustomEntityBundle\Controller;

use Pim\Component\ReferenceData\Repository\ReferenceDataRepositoryInterface;
use PimEnterprise\Bundle\UIBundle\Controller\AjaxOptionController as BaseAjaxOptionController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Overridden because the data locale is not taken into account in the AjaxOptionController
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class EnterpriseAjaxOptionController extends BaseAjaxOptionController
{
    /**
     * {@inheritdoc}
     */
    public function listAction(Request $request)
    {
        $query = $request->query;
        $repository = $this->doctrine->getRepository($query->get('class'));

        if ($repository instanceof ReferenceDataRepositoryInterface) {
            $options = $query->get('options', []) + ['dataLocale' => $query->get('dataLocale')];

            $choices = ['results' => $repository->findBySearch($query->get('search'), $options)];

            return new JsonResponse($choices);
        }

        return parent::listAction($request);
    }
}
