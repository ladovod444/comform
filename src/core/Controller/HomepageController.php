<?php


namespace Core\Controller;

use BaksDev\Products\Product\Repository\Cards\ProductPromo\ProductPromoRepository;
use Core\Repository\PageProduct\PageProductInterface;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Products\Product\Repository\Cards\ModelOrProduct\ModelOrProductInterface;
//use Core\Repository\ProductPromo\ProductPromoRepository;
use DirectoryIterator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class HomepageController extends AbstractController
{

    private string $publicDir;

    private string $bannersDir;

    private array $banners = [];

    private int|null $dirCount = null;

    #[Route('/', name: 'user.homepage', priority: 999)]
    public function index(
        PageProductInterface $liderProduct,
        ModelOrProductInterface $modelOrProduct,
        ProductPromoRepository $productPromoRepository,
        KernelInterface $appKernel,
    ): Response
    {

        $this->publicDir = $appKernel->getProjectDir().DIRECTORY_SEPARATOR.'public';
        $this->bannersDir = $this->publicDir.DIRECTORY_SEPARATOR.'banners';

        $this->scan(new DirectoryIterator($this->bannersDir));

        $cards = $modelOrProduct
            ->maxResult(16)
            ->toArray();

//        dump($cards);

        $promoProducts = $productPromoRepository
            ->maxResult(4)
            ->toArray();

//        dd($promoProducts);

        return $this->render([
            'cards' => $cards,
            'promoProducts' => $promoProducts,
            'banners' => $this->banners,
//            'liderProduct' => $liderProduct->fetchPageProductAssociative()
        ]);
    }


    private function scan(
        DirectoryIterator $iterator,
        ?string $out = null,
        ?DirectoryIterator $lastIterator = null
    ): void
    {

        foreach($iterator as $in)
        {
            if(isset($this->banners[$in->getFilename()]))
            {
                continue;
            }

            if($in->isDot())
            {
                continue;
            }

            if($in->isDir())
            {
                $dirIterator = new DirectoryIterator($in->getRealPath());

                $lastIterator = 0;

                foreach($dirIterator as $item)
                {
                    if($item->isDot())
                    {
                        continue;
                    }

                    $lastIterator++;
                }

                $this->dirCount = $lastIterator;

                $this->scan($dirIterator, $in->getFilename(), $iterator);
            }
            else
            {

                if(null !== $out)
                {
                    $this->dirCount -= 1;

                    $this->banners[$out][] = str_replace($this->publicDir, '', $in->getRealPath());

                    if($this->dirCount === 0)
                    {
                        $lastIterator->next();
                        $this->scan($lastIterator);
                    }
                }
                else
                {
                    $this->banners[$in->getFilename()] = str_replace($this->publicDir, '', $in->getRealPath());
                }
            }
        }

    }

}
