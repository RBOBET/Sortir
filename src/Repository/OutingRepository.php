<?php

namespace App\Repository;

use App\Entity\Outing;
use App\Entity\Participant;
use App\Entity\Place;
use App\Form\Model\OutingFilterModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use function Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Outing>
 *
 * @method Outing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Outing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Outing[]    findAll()
 * @method Outing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private Security $security)
    {
        parent::__construct($registry, Outing::class);
    }

    public function save(Outing $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Outing $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function findListWithoutFilter(){
        $qb = $this->createQueryBuilder('outing');
        $qb
            ->leftJoin('outing.planner', 'part')
            ->addSelect('part')
            ->leftJoin('outing.status', 'status')
            ->addSelect('status')
            ->leftJoin('outing.plannerCampus', 'campus')
            ->addSelect('campus')
            ->leftJoin('outing.place', 'place')
            ->addSelect('place')
            ->leftJoin('outing.participants', 'out_part')
            ->addSelect('out_part')
            //je ne récupère pas les sorties archivées
            ->andWhere('status.id != :statusArchived')
            ->setParameter('statusArchived', 7)
            ->addOrderBy('outing.dateTimeStart');

        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function findAllOutings(){
        $qb = $this->createQueryBuilder('outing');
        $qb
            ->leftJoin('outing.planner', 'part')
            ->addSelect('part')
            ->leftJoin('outing.status', 'status')
            ->addSelect('status')
            ->leftJoin('outing.plannerCampus', 'campus')
            ->addSelect('campus')
            ->leftJoin('outing.place', 'place')
            ->addSelect('place')
            ->leftJoin('outing.participants', 'out_part')
            ->addSelect('out_part');

        $query = $qb->getQuery();
        return $query->getResult();
    }


    public function findOutingsWithFilter(OutingFilterModel $filter){
        $currentUser = $this->security->getUser();
        $qb = $this->createQueryBuilder('outing');
        $qb
            ->leftJoin('outing.planner', 'part')
            ->addSelect('part')
            ->leftJoin('outing.status', 'status')
            ->addSelect('status')
            ->leftJoin('outing.plannerCampus', 'campus')
            ->addSelect('campus')
            ->leftJoin('outing.place', 'place')
            ->addSelect('place')
            ->leftJoin('outing.participants', 'out_part')
            ->addSelect('out_part')
            ->andWhere('status.id != :statusArchived')
            ->setParameter('statusArchived', 7)
            ->addOrderBy('outing.dateTimeStart');

        if ($filter->getCampus()->getId() != 1){
            $qb
                ->andWhere('outing.plannerCampus = :campus')
                ->setParameter('campus', $filter->getCampus());
        }
        if ($filter->getNameContains() != null){
            $qb
                ->andWhere('outing.title LIKE :nameContains')
                ->setParameter('nameContains', '%'.$filter->getNameContains().'%');
        }

        if ($filter->getStartDate()){
            $qb
                ->andWhere('outing.dateTimeStart >= :startDate')
                ->setParameter('startDate', $filter->getStartDate());
        }

        if ($filter->getEndDate()){
            $qb
                ->andWhere('outing.dateTimeStart <= :endDate')
                ->setParameter('endDate', $filter->getEndDate());
        }

        if (($filter->isPlanner())){
            $qb
                ->andWhere('part = :userP')
                ->setParameter('userP', $currentUser);
        }

        if ($filter->isRegistered()){
            $qb
                ->andWhere(':userR MEMBER OF outing.participants')
                ->setParameter('userR', $currentUser);
        }

        if ($filter->isNotRegistered()){
            $qb
                ->andWhere(':userNR NOT MEMBER OF outing.participants')
                ->setParameter('userNR', $currentUser);
        }

        if ($filter->isOutingIsPast()){
            $qb
                ->andWhere('status = :statusFinished')
                ->setParameter('statusFinished', 5);
        }

        $query = $qb->getQuery();
        return $query->getResult();

    }

//    public function selectedPlace(Place $selectedPlace){
//
//
//
//        return $selectedPlace;
//    }
}
