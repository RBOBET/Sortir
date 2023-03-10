<?php

namespace App\Controller;


use App\Entity\Outing;
use App\Entity\Participant;
use App\Entity\Status;
use App\Form\Model\OutingFilterModel;
use App\Form\Model\OutingFilterType;
use App\Form\OutingType;
use App\Repository\OutingRepository;
use App\Repository\ParticipantRepository;
use App\Repository\StatusRepository;
use App\Service\OutingsStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;


#[Route('/outing', name: 'outing_')]
class OutingController extends AbstractController
{

    #[Route('/add', name: 'add')]

    public function add(
        Request          $request,
        OutingRepository $outingRepository,
        StatusRepository $statusRepository,
        ParticipantRepository $participantRepository
    ): Response
    {
        /**
         * @var Participant $planner
         */
        $planner = $this->getUser();
        $outing = new Outing();
        $outing->setPlanner($planner);
        $outing->setPlannerCampus($planner->getCampus());

        $outingForm = $this->createForm(OutingType::class, $outing);

        $outingForm
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer pour plus tard'])

            ->add('saveAndPublish', SubmitType::class, [
                'label' => 'Publier la sortie'])
        ;


        $outingForm->handleRequest($request);


        if ($outingForm->isSubmitted() && $outingForm->isValid()) {


            if ($outingForm->get('saveAndPublish')->isClicked()) {

                $outing->setStatus($statusRepository->find(2));

                if ($outingForm->get('registerPlanner')->getData()){
                $outing->addParticipant($planner);
                $planner->addOuting($outing);
                $participantRepository->save($planner, true);
            }

                $outingRepository->save($outing, true);

                //ajout flash parce qu'on est des BG qui font ??a bien
                $this->addFlash("success", "Votre sortie est en ligne !");

            } else {

                $outing->setStatus($statusRepository->find(1));

                if ($outingForm->get('registerPlanner')->getData()){
                    $outing->addParticipant($planner);
                    $planner->addOuting($outing);
                    $participantRepository->save($planner, true);
                }

                $outingRepository->save($outing, true);

                $this->addFlash("success",
                    "Sortie cr????e avec succ??s :-) n'oubliez pas de la publier ;-) ");

            }


            return $this->redirectToRoute("outing_show", ['id' => $outing->getId()]);

        }

        return $this->render('outing/add.html.twig', [
            'outingForm' => $outingForm->createView()
        ]);

    }


    #[Route('/update/{id}', name: 'update')]
    public function update(Request $request,
                           OutingRepository $outingRepository,
                           StatusRepository $statusRepository,
                           int $id,
    ): Response

    {
        $outing = $outingRepository->find($id);
        $outingForm = $this->createForm(OutingType::class, $outing);

        if ($this->getUser()->getId() != $outing->getPlanner()->getId()){
            $this->addFlash('warning', 'Vous n\'??tes pas l\'utilisateur qui a organis?? cette sortie');
            $this->redirectToRoute('outing_list');
        }

        $outingForm
            ->add('save', SubmitType::class, [
                'label' => 'Modifier'])
        ;


        $outingForm->handleRequest($request);

        if ($outingForm->isSubmitted() && $outingForm->isValid()) {

            $outingRepository->save($outing, true);

            $this->addFlash("success",
                "Sortie mise ?? jour avec succ??s");


            return $this->redirectToRoute("outing_show", ['id' => $outing->getId()]);
        }



        return $this->render('outing/update.html.twig', [
            'outingForm' => $outingForm->createView(),
            'outing' => $outing
        ]);

    }


    #[Route('/remove/{id}', name: 'remove')]
    public function remove(int $id, OutingRepository $outingRepository): Response
    {
        $outing = $outingRepository->find($id);

        if ($this->getUser()->getId() != $outing->getPlanner()->getId()){
            $this->addFlash('warning', 'Vous n\'??tes pas l\'utilisateur qui a organis?? cette sortie');

        } else {
            $outingRepository->remove($outing, true);
            $this->addFlash("warning", "La sortie a ??t?? supprim??e, cette action est irr??versible");
        }
        return $this->redirectToRoute('outing_list');
    }

    #[Route('/cancel/{id}', name: 'cancel')]
    public function cancel(
        int $id,
        OutingRepository $outingRepository,
        StatusRepository $statusRepository
    ): Response
    {
        $outing = $outingRepository->find($id);

        if ($this->getUser()->getId() != $outing->getPlanner()->getId()) {

            $this->addFlash('warning', 'Vous n\'??tes pas l\'utilisateur qui a organis?? cette sortie');

        } else {
            $outing->setStatus($statusRepository->find(6));

            $this->addFlash("warning", "Votre sortie a ??t?? annul??e");
        }

        return $this->redirectToRoute('outing_list');
    }



    #[Route('/list', name: 'list')]
    public function list(Request $request, OutingRepository $outingRepository, OutingsStatus $outingsStatus): Response
    {
        //je mets ?? jour tous les statuts des sorties suivant la date du jour
        $outingsStatus->updateStatus();
        //je r??cup??re un tableau de statuts
        $statusCodes = $this->getParameter('status_codes');

        $outingFilter = new OutingFilterModel();

        $outingFilterForm = $this->createForm(OutingFilterType::class, $outingFilter);
        $outingFilterForm->handleRequest($request);
        dump($outingFilter);

        if ($outingFilterForm->isSubmitted() && $outingFilterForm->isValid()) {
            dump($outingFilter);
            $outings = $outingRepository->findOutingsWithFilter($outingFilter);
        } else {
            $outings = $outingRepository->findListWithoutFilter();
        }
        $i = 0;
        foreach ($outings as $out) {
            if ($out->getStatus()->getId() == 1 &&
                $out->getPlanner()->getId() != $this->getUser()->getId()) {
                unset($outings[$i]);
            }
            $i++;
        }


        return $this->render('outing/list.html.twig', [
            'outings' => $outings,
            'statusCodes' => $statusCodes,
            'filterForm' => $outingFilterForm->createView()
        ]);
    }

    #[Route('/show/{id}', name: 'show')]
    public function show(int $id, OutingRepository $outingRepository): Response
    {
        $outing = $outingRepository->find($id);

        if (!$outing) {
            throw $this->createNotFoundException("Oops! il n'existe pas !");
        }

        return $this->render('outing/show.html.twig', [
            'outing' => $outing
        ]);
    }

    #[Route('/register/{id}', name: 'register')]
    public function register(int $id,
                             OutingRepository $outingRepository,
                             ParticipantRepository $participantRepository,
                                StatusRepository $statusRepository): Response
    {
        $outing = $outingRepository->find($id);

        /**
         * @var Participant $user
         */

        $user = $this->getUser();
        //si le nb de partiicipants max est atteint
        if ((count($outing->getParticipants()) + 1) == $outing->getNbParticipantsMax()){
            $outing->setStatus($statusRepository->find(3));
        }
        $outing->addParticipant($user);
        $user->addOuting($outing);

        $participantRepository->save($user, true);
        $outingRepository->save($outing, true);

        $this->addFlash("success", "Vous ??tes enregistr?? !");

        return $this->redirectToRoute('outing_list');
    }


    #[Route('desist/{id}', name: 'desist')]
    public function desist(int $id, OutingRepository $outingRepository, ParticipantRepository $participantRepository): Response
    {
        $outing = $outingRepository->find($id);

        /**
         * @var Participant $user
         */

        $user = $this->getUser();
        $outing->removeParticipant($user);
        $user->removeOuting($outing);

        $participantRepository->save($user, true);
        $outingRepository->save($outing, true);
        $this->addFlash("success", "Vous ??tes d??sinscrit !");


        return $this->redirectToRoute('outing_list');
    }




//    public function listPlacesRelatedToCity(Request $request)
//    {
//        $entityManager = $this->getDoctrine()->getManager();
//        $placeRepository = $entityManager->getRepository("App:Place");
//
//        $place = $placeRepository->createQueryBuilder("p")
//            ->where("p.city = cityId")
//            ->setParameter("cityId", $request->query->get("cityId"))
//            ->getQuery()
//            ->getResult();
//
//        $responseArray = array();
//
//        foreach ($place as $place) {
//            $responseArray[] = array(
//                "id" => $place->getId(),
//                "name" => $place->getName()
//            );
//        }
//
//        return new JsonResponse($responseArray);
//    }



}


