<?php


namespace Core\Controller;

use BaksDev\Products\Category\Repository\AllCategory\AllCategoryInterface;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;
use BaksDev\Products\Product\Forms\ProductCategoryFilter\User\ProductCategoryFilterDTO;
use BaksDev\Products\Product\Forms\ProductCategoryFilter\User\ProductCategoryFilterForm;
use BaksDev\Products\Product\Forms\ProductFilter\Admin\ProductFilterDTO;
use BaksDev\Products\Product\Forms\ProductFilter\Admin\ProductFilterForm;
use BaksDev\Products\Product\Repository\Cards\ProductPromo\ProductPromoInterface;
use Core\Repository\PageProduct\PageProductInterface;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Products\Product\Repository\Cards\ModelOrProduct\ModelOrProductInterface;

//use Core\Repository\ProductPromo\ProductPromoRepository;
use DirectoryIterator;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
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
        Request $request,
        PageProductInterface $liderProduct,
        ModelOrProductInterface $modelOrProduct,
        ProductPromoInterface $productPromoRepository,

        AllCategoryInterface $allCategoryRec,

        FormFactoryInterface $formFactory,

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




        // FILTER
        $categories = $allCategoryRec->getRecursive();

//        dump($categories);

//        dd($categories);

        $kuhni = array_filter($categories, function(array $category) {
            return $category['category_url'] === 'kuhni';
//            return $category['category_url'] === 'kuhni_loft';
        });

//        dump($kuhni);

        $categoryUid = new CategoryProductUid(current($kuhni)['id']);

        //Фильтр по параметрам
        $productCategoryFilterDTO = new ProductCategoryFilterDTO($categoryUid);
//        $productCategoryFilterDTO = new ProductCategoryFilterDTO();
        $productFilterForm = $this->createForm(ProductCategoryFilterForm::class,
            $productCategoryFilterDTO,
            ['action' => $this->generateUrl('products-product:user.catalog.index')]
        );
        $productFilterForm->handleRequest($request);


        //////////////////////////////////////////////////////////////
//        $categoryUid = null;
//
//        /** Из сессии */
//        if($sessionFromForm = $request->getSession()->get(md5(ProductFilterForm::class)))
//        {
//            $sessionData = base64_decode($sessionFromForm);
//            $categoryId = json_decode($sessionData, true, 512, JSON_THROW_ON_ERROR);
//
//            if(isset($categoryId['category']))
//            {
//                $categoryUid = new CategoryProductUid($categoryId['category']);
//            }
//        }
//
//        /** Из формы */
//        $post = $request->request->all();
//        if(isset($post['product_category_filter_form']['category']))
//        {
//            $categoryUid = new CategoryProductUid($post['product_category_filter_form']['category']);
//        }
//
//        /** Фильтр с главной страницы */
//        $productCategoryFilterDTO = new ProductCategoryFilterDTO($categoryUid);
//
//        $productFilterForm = $this->createForm(ProductCategoryFilterForm::class, $productCategoryFilterDTO);
//        $productFilterForm->handleRequest($request);
//
//        /** Свойства продукции, участвующие в фильтрации */
//        $propertyFields = null;
//        if($productFilterForm->isSubmitted() && $productFilterForm->isValid())
//        {
//            foreach($productFilterForm->all() as $item)
//            {
//                if($item instanceof Form && !empty($item->getViewData()))
//                {
//                    if($item->getConfig()->getMapped())
//                    {
//                        continue;
//                    }
//
//                    $propertyFields[$item->getName()] = $item->getNormData();
//                }
//            }
//        }
//
//
        //////////////////////////////////////////////////////////////


//        dd($productFilterForm);


        return $this->render([
            'cards' => $cards,
            'promoProducts' => $promoProducts,
            'banners' => $this->banners,

            'furniture_select' => $productFilterForm->createView(),
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
