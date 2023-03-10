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
    ): Response
    {
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

                    $outingRepository->save($outing, true);

                    //ajout flash parce qu'on est des BG qui font ça bien
                    $this->addFlash("success", "Votre sortie est en ligne !");

                } else {

                    $outing->setStatus($statusRepository->find(1));

                    $outingRepository->save($outing, true);

                    $this->addFlash("success",
                        "Sortie créée avec succès :-) n'oubliez pas de la publier ;-) ");

                }


                return $this->redirectToRoute("outing_show", ['id' => $outing->getId()]);
//           dd($outing);
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

        $outingForm
            ->add('save', SubmitType::class, [
            'label' => 'Modifier'])
            ;


        $outingForm->handleRequest($request);

        if ($outingForm->isSubmitted() && $outingForm->isValid()) {

            $outingRepository->save($outing, true);

            $this->addFlash("success",
                "Sortie mise à jour avec succès");


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

    if ($outing) {
        $outingRepository->remove($outing);
        $this->addFlash("warning", "La sortie a été supprimée, cette action est irréversible");
    } else {
        throw $this->createNotFoundException(("Cette sortie ne peut pas être supprimée"));
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

        if ($outing) {
            $outing->setStatus($statusRepository->find(5));
            $this->addFlash("warning", "Votre sortie a été annulée");
        } else {
            throw $this->createNotFoundException(("Cette sortie ne peut pas être annulée"));
        }

        return $this->redirectToRoute('outing_list');
    }



    #[Route('/list', name: 'list')]
    public function list(OutingRepository $outingRepository, Request $request): Response
    {
        $statusCodes = $this->getParameter('status_codes');

        $outingFilter = new OutingFilterModel();
        $outingFilter->setCampus($this->getUser()->getCampus());
        $outingFilter->setStartDate(new \DateTime('-1 year'));
        $outingFilter->setEndDate(new \DateTime('+ 1 year'));

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
    public function register(int $id, OutingRepository $outingRepository, ParticipantRepository $participantRepository): Response
{
    $outing = $outingRepository->find($id);

    /**
     * @var Participant $user
     */

    $user = $this->getUser();
    $outing->addParticipant($user);
    $user->addOuting($outing);

    $participantRepository->save($user, true);
    $outingRepository->save($outing, true);
    $this->addFlash("success", "Vous êtes enregistré !");

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
    $this->addFlash("success", "Vous êtes désinscrit !");


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


