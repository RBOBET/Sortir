<?php

namespace App\Controller;

use App\Entity\Place;
use App\Form\NewPlaceType;
use App\Repository\PlaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/place', name: 'place_')]
class PlaceController extends AbstractController
{
    #[Route('/add', name: 'add')]
    public function newPlace(Request $request, PlaceRepository $placeRepository
    ): Response
    {
        $place = new Place();
        $newPlaceForm = $this->createForm(NewPlaceType::class, $place);

        $newPlaceForm->handleRequest($request);

        if ($newPlaceForm->isSubmitted() && $newPlaceForm->isValid()) {

     //       if ($newPlaceForm->get('create')->isClicked()) {

                $placeRepository->save($place, true);

                $this->addFlash("success", "Votre lieu a été ajouté !");

//            } else {
//
//                $outing->setStatus($statusRepository->find(1));
//
//                $outingRepository->save($outing, true);
//
//                $this->addFlash("success",
//                    "Sortie créée avec succès :-) n'oubliez pas de la publier ;-) ");
//
//            }


            return $this->redirectToRoute("outing_add");

        }




        return $this->render('place/add.html.twig', [
            'newPlaceForm' => $newPlaceForm->createView()

        ]);
    }
}
