<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Model\SearchData;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginatorInterface)
    {
        parent::__construct($registry, Recipe::class);
    }

    /**
     * @return Recipe[]
     */
    public function findRecipeDurationLowerThan(int $duration): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.duration <= :duration')
            ->orderBy('r.duration', 'ASC')
            ->setParameter('duration', $duration)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $page
     * @return PaginationInterface
     */
    public function findRecipes(int $page): PaginationInterface
    {
        $data =  $this->createQueryBuilder('r')
            ->getQuery()
            ->getResult();

        $recipes = $this->paginatorInterface->paginate($data, $page, 9);
        return $recipes;
    }

    //    /**
    //     * @return Recipe[] Returns an array of Recipe objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Recipe
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    /**
     * GET recipes by search data
     * @param SearchData $searchData
     * @return PaginationInterface
     */
    public function findRecipesBySearch(SearchData $searchData): PaginationInterface
    {
        $query = $this->createQueryBuilder('r')
            ->addOrderBy('r.title', 'ASC');

        if (!empty($searchData->q)) {
            $query = $query
                ->andWhere('r.title LIKE :q')
                ->setParameter('q', '%' . $searchData->q . '%');
        }

        $data = $query->getQuery()->getResult();

        return $this->paginatorInterface->paginate($data, $searchData->page, 9);
    }
}
