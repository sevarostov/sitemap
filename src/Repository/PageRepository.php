<?php

namespace App\Repository;

use App\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Page>
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

	/**
	 * @param $value
	 * @param $max
	 *
	 * @return Page[] Returns an array of Page objects
	 */
	public function findByName($value, $max = null): array {
		$qb = $this->createQueryBuilder('p')
			->join('p.template', 't', Expr\Join::WITH, 't.id = p.template')
			->andWhere('t.context = :val')
			->setParameter('val', $value)
			->orderBy('p.id', 'ASC');

		if ($max) {
			$qb->setMaxResults(10);
		}

		return $qb
			->getQuery()
			->getResult();
	}

    //    public function findOneBySomeField($value): ?Page
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
