<?php

namespace App\Controller;


use App\Entity\Outing;
use App\Form\Model\OutingFilterModel;
use App\Form\Model\OutingFilterType;
use App\Form\OutingType;
use App\Repository\OutingRepository;
use App\Repository\StatusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


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

    #[Route('/list', name: 'list')]
    public function list(OutingRepository $outingRepository): Response
    {
        $outingFilter = new OutingFilterModel();
        $outingFilter->setCampus($this->getUser()->getCampus());
        $outingFilterForm = $this->createForm(OutingFilterType::class, $outingFilter);

        if ($outingFilterForm->isSubmitted() && $outingFilterForm->isValid()){
            $outings = $outingRepository->findOutingsWithFilter($outingFilter);
        } else {
            $outings = $outingRepository->findListWithoutFilter();
        }
        $i = 0;
        foreach($outings as $out) {
            if($out->getStatus()->getId() == 1 &&
                $out->getPlanner()->getId() != $this->getUser()->getId()) {
                unset($outings[$i]);
            }
            $i++;
        }



        return $this->render('outing/list.html.twig', [
            'outings' => $outings,
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

    #[Route('/register/{id}',name: 'register')]
    public function  register (int $id, OutingRepository $outingRepository): Response
    {
        $outing=$outingRepository->find($id);
        $this->getUser();

        return $this->render('outing/list.html.twig');
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


