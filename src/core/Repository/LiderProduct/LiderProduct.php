<?php
/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace Core\Repository\LiderProduct;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Info\CategoryProductInfo;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;
use BaksDev\Products\Product\Entity\Category\ProductCategory;
use BaksDev\Products\Product\Entity\Info\ProductInfo;
use BaksDev\Products\Product\Entity\Offers\ProductOffer;
use BaksDev\Products\Product\Entity\Offers\Quantity\ProductOfferQuantity;
use BaksDev\Products\Product\Entity\Offers\Variation\Modification\ProductModification;
use BaksDev\Products\Product\Entity\Offers\Variation\Modification\Quantity\ProductModificationQuantity;
use BaksDev\Products\Product\Entity\Offers\Variation\ProductVariation;
use BaksDev\Products\Product\Entity\Offers\Variation\Quantity\ProductVariationQuantity;
use BaksDev\Products\Product\Entity\Price\ProductPrice;
use BaksDev\Products\Product\Entity\Product;
use BaksDev\Products\Product\Entity\Trans\ProductTrans;

final class LiderProduct implements LiderProductInterface
{
    //	private Connection $connection;
    //
    //	private Locale $locale;
    //
    //
    //	public function __construct(
    //		Connection $connection,
    //		TranslatorInterface $translator,
    //	)
    //	{
    //		$this->connection = $connection;
    //		$this->locale = new Locale($translator->getLocale());
    //	}

    private DBALQueryBuilder $DBALQueryBuilder;

    public function __construct(DBALQueryBuilder $DBALQueryBuilder)
    {
        $this->DBALQueryBuilder = $DBALQueryBuilder;
    }


    public function fetchPageProductAssociative(CategoryProductUid|string $category = null): array
    {
        if(is_string($category))
        {
            $category = new CategoryProductUid($category);
        }

        $qb = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $qb->from(Product::class, 'product');

        $qb
            ->addSelect('product_trans.name AS product_name')
            ->leftJoin(
                'product',
                ProductTrans::class,
                'product_trans',
                'product_trans.event = product.event AND product_trans.local = :local'
            );


        /** Цена товара */
        $qb->leftJoin(
            'product',
            ProductPrice::class,
            'product_price',
            'product_price.event = product.event'
        );

        /* ProductInfo */

        $qb
            ->addSelect('product_info.url')
            ->addSelect('product_info.sort')
            ->leftJoin(
                'product',
                ProductInfo::class,
                'product_info',
                'product_info.product = product.id'
            );

        /** Торговое предложение */

        $qb->leftJoin(
            'product',
            ProductOffer::class,
            'product_offer',
            'product_offer.event = product.event'
        );

        $qb->leftJoin(
            'product_offer',
            ProductOfferQuantity::class,
            'product_offer_quantity',
            'product_offer_quantity.offer = product_offer.id'
        );

        /** Множественный вариант */

        $qb->leftOneJoin(
            'product_offer',
            ProductVariation::class,
            'product_offer_variation',
            'product_offer_variation.offer = product_offer.id'
        );

        $qb->leftJoin(
            'product_offer_variation',
            ProductVariationQuantity::class,
            'product_variation_quantity',
            'product_variation_quantity.variation = product_offer_variation.id'
        );


        /** Модификация множественного варианта */

        $qb->leftJoin(
            'product_offer_variation',
            ProductModification::class,
            'product_offer_modification',
            'product_offer_modification.variation = product_offer_variation.id'
        );

        $qb->leftJoin(
            'product_offer_modification',
            ProductModificationQuantity::class,
            'product_modification_quantity',
            'product_modification_quantity.modification = product_offer_modification.id'
        );


        /* Категория */
        $qb->leftJoin(
            'product',
            ProductCategory::class,
            'product_event_category',
            '
                product_event_category.event = product.event AND 
                product_event_category.root = true
            '
        );

        if($category instanceof CategoryProductUid)
        {
            $qb
                ->andWhere('product_event_category.category = :category')
                ->setParameter(
                    'category',
                    $category,
                    CategoryProductUid::TYPE
                );

        }


        $qb->leftJoin(
            'product_event_category',
            CategoryProduct::class,
            'category',
            'category.id = product_event_category.category'
        );


        $qb
            ->addSelect('category_info.url AS category_url')
            ->leftJoin(
                'product_event_category',
                CategoryProductInfo::class,
                'category_info',
                'category_info.event = category.event'
            );


        /** Только при наличии */
        $qb->andWhere("
 		
 			CASE
			   WHEN product_modification_quantity.quantity IS NOT NULL THEN (product_modification_quantity.quantity - product_modification_quantity.reserve)
			   WHEN product_variation_quantity.quantity IS NOT NULL THEN (product_variation_quantity.quantity - product_variation_quantity.reserve)
			   WHEN product_offer_quantity.quantity IS NOT NULL THEN (product_offer_quantity.quantity - product_offer_quantity.reserve)
			   WHEN product_price.quantity  IS NOT NULL THEN (product_price.quantity - product_price.reserve)
			   ELSE 0
			END > 0
 		
 		");

        $qb->addOrderBy('SUM(product_modification_quantity.reserve)', 'DESC');
        $qb->addOrderBy('SUM(product_variation_quantity.reserve)', 'DESC');
        $qb->addOrderBy('SUM(product_offer_quantity.reserve)', 'DESC');
        $qb->addOrderBy('SUM(product_price.reserve)', 'DESC');

        $qb->addOrderBy('product_info.sort', 'DESC');


        $qb->setMaxResults(10);
        $qb->allGroupByExclude();


        return $qb
            ->enableCache('product-products', 86400, false)
            ->fetchAllAssociative();

    }
}