<?php

namespace App\Repository;

use App\Entity\Outing;
use App\Entity\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
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
        $currentUser = $this->security->getUser();
        $idUser = $currentUser->getId();
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
            ->andWhere('status.id != :statusFinished')
            ->setParameter('statusFinished', 5);


        /*$qb

            //récupérer sorties crées que si l'organisateur est l'utilisateur connecté
            ->andWhere($qb->expr()->andX(
                $qb->expr()->eq('outing.id', ':statusCreated'),
                $qb->expr()->eq('outing.planner.id', )
            ))
            ->andWhere($qb->expr()->neq('outing.status.id', ':statusFinished'))
            ->setParameter('statusFinished', 5)
            ->setParameter('statusCreated', 1)
            ->addOrderBy('outing.dateTimeStart');*/
        $query = $qb->getQuery();
        return $query->getResult();
    }
}
