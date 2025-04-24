<?php

namespace Sale\Controller;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Products\Product\Repository\Cards\ProductPromo\ProductPromoRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class SaleController extends AbstractController
{
    #[Route('/sale', name: 'index')]
    public function index(
        ProductPromoRepository $productPromoRepository,
    ): Response
    {

//        dd(__METHOD__);

        $promoProducts = $productPromoRepository
            ->maxResult(16)
            ->toArray();

        return $this->render([
            'promoProducts' => $promoProducts,
//            'controller_name' => 'ExampleController',
        ]);
    }
}
