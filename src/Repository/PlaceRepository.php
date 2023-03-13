<?php

namespace App\Repository;

use App\Entity\City;
use App\Entity\Place;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Place>
 *
 * @method Place|null find($id, $lockMode = null, $lockVersion = null)
 * @method Place|null findOneBy(array $criteria, array $orderBy = null)
 * @method Place[]    findAll()
 * @method Place[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Place::class);
    }

    public function save(Place $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Place $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findPlacesByCity(int $id)
    {

        $qb = $this->createQueryBuilder('place');   //il sait qu'on requête sur la table de ce repo et fait automatiquement le select
        $qb                                              //place n'est qu'un alias
            ->leftJoin('place.city', 'city')   //entité.attribut sur lequel je veux joindre ma table
            ->addSelect('city')                   //select sur la 2eme table (celle de city) pour finaliser la jointure
            ->andWhere('city.id = :id')                     //on précise sur quelle city en particulier
            ->setParameter('id', $id)              //en l'occurence celle passée en paramètre de la fonction
            ->addOrderBy('place.name')              //triées par ordre alphabétique ça fait plaisir
        ;

        $query = $qb->getQuery();                       //on crée une requête à partir du query builder
            return $query->getResult();                 //et on en retourne le résultat

    }








//    /**
//     * @return Place[] Returns an array of Place objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Place
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
