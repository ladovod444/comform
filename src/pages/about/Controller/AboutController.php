<?php

namespace About\Controller;

use BaksDev\Core\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class AboutController extends AbstractController
{
    #[Route('/about', name: 'index')]
    public function index(): Response
    {
        return $this->render([
            'controller_name' => 'ExampleController',
        ]);
    }
}
