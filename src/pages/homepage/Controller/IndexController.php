<?php

namespace Home\Page\Controller;

use App\Core\Repository\PageProduct\PageProductInterface;
use BaksDev\Core\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class IndexController extends AbstractController
{
    #[Route('/', name: 'index', priority: 10)]
    public function index(PageProductInterface $liderProduct): Response
    {

        return $this->render([
            'liderProduct' => $liderProduct->fetchPageProductAssociative()
        ]);
    }

}
