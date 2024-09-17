<?php

namespace App\Repository;

use App\Entity\CartProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CartProduct>
 */
class CartProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartProduct::class);
    }

    public function findByCartId(int $cartId): array
    {
        return $this->createQueryBuilder('cp')
            ->select('
                cp.id AS cart_product_id,
                cp.amount,
                p.id AS product_id,
                p.name AS product_name,
                p.price AS product_price,
                p.weight AS product_weight
            ')
            //->from('App\Entity\CartProduct', 'cp')
            //->leftJoin('cp.product', 'p') // no idea
            ->leftJoin(
                'App\Entity\Product',
                'p',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'cp.productId = p.id'
            )
            ->where('cp.cartId = :cartId')
            ->setParameter('cartId', $cartId)
            ->getQuery()->getResult();
    }

    //    /**
    //     * @return CartProduct[] Returns an array of CartProduct objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CartProduct
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
