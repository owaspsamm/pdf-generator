<?php

declare(strict_types=1);

namespace App\Controller\Application;

use App\Service\SammModelGeneratorService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class IndexController extends AbstractController
{
    /**
     * @param SammModelGeneratorService $sammModelGeneratorService
     * @return Response
     */
    #[Route('/', name: 'index', methods: ["GET"])]
    public function sammModelPdf(
        SammModelGeneratorService $sammModelGeneratorService
    ): Response {
//        exit("done");
        return new BinaryFileResponse($sammModelGeneratorService->generate());
    }
}