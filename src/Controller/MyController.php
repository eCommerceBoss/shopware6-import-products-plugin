<?php declare(strict_types=1);

namespace Sas\SyncerModule\Controller;

use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @RouteScope(scopes={"api"})
 */
class MyController extends AbstractController
{
    /**
     * @Route("/api/v{version}/sas-syncer/my-api-action", name="api.action.sas-syncer.my-api-action", methods={"GET"})
     */
    public function myActionApi(): JsonResponse
    {
        return new JsonResponse(['You successfully created your first controller route']);
    }
}
