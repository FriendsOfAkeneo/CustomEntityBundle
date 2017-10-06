<?php

namespace Pim\Bundle\CustomEntityBundle\Action\Rest;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeleteAction extends AbstractRestAction
{
    /**
     * {@inheritdoc}
     */
    protected function doExecute(Request $request): JsonResponse
    {
        $entity = $this->findEntity($request);
        $this->getManager()->remove($entity);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'rest_delete';
    }
}
