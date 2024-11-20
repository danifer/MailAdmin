<?php

namespace App\Repository;

use App\Entity\MailAlias;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MailAlias>
 */
class MailAliasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MailAlias::class);
    }

    /**
     * @return MailAlias[]
     */
    public function findDestinationsContainingString(string $string): array
    {
        $arr = $this->createQueryBuilder('m')
            ->andWhere('m.destination LIKE :val')
            ->setParameter('val', '%' . $string . '%')
            ->getQuery()
            ->getResult()
        ;

        return array_filter($arr, callback: function($item) use ($string) {
            $destinations = explode(',', $item->getDestination());
            array_walk($destinations, 'trim');
            if (in_array($string, $destinations, true)) {
                return true;
            }
            return false;
        });
    }

    //    /**
    //     * @return MailAlias[] Returns an array of MailAlias objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?MailAlias
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
