<?php

namespace App\Controller\Api;
use App\Entity\City;
use App\Repository\PlaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('Api/Place', name: 'api_place_')]
class PlaceController extends AbstractController
{
    #[Route('/{id}', name: 'find_places')]
    public function findPlacesByCity(int $id, PlaceRepository $placeRepository){

        $places = $placeRepository->findPlacesByCity($id);
        $placesArray = [];
        foreach ($places as $place){
            $placesArray[] = [
                'id' => $place->getId(),
                'nom' => $place->getName()
            ];
        }
        return $this->json($placesArray, 200, [], ['groups' => 'place_api']);
    }

}