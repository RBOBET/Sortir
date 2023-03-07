<?php

namespace App\Controller;

use App\Entity\Season;
use App\Form\SeasonType;
use App\Repository\OutingRepository;
use App\Repository\SeasonRepository;
use App\Repository\SerieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/outing', name: 'outing_')]
class OutingController extends AbstractController

//TODO discuss about naming convention (could have more than one add)
//TODO create form on the other side
{

    #[Route('/add', name: 'add')]
    public function add(Request $request, OutingRepository $outingRepository): Response
    {
        $outing = new Outing();
        $outingForm = $this->createForm(OutingType::class, $outing);

        $outingForm->handleRequest($request);

        if($outingForm->isSubmitted() && $outingForm->isValid()){


            $outingRepository->save($outing, true);

            //ajout flash parce qu'on est des BG qui font ça bien
            $this->addFlash("success", "outing successfully added");

            //redirection
            return $this->redirectToRoute("outing_show", ['id' => $outing->getOuting()->getId()]);
            //           dd($outing);
        }

        return $this->render('outing/add.html.twig', [
            'outingForm' => $outingForm->createView()
        ]);
    }


    #[Route('/remove/{id}', name: 'remove')]
    public function remove(int $id, OutingRepository $outingRepository){
        $outing = $outingRepository->find($id);

        if($outing){
            $outingRepository->remove($outing);
            $this->addFlash("warning","The outing has been deleted, this action cannot be undown" );
        } else {
            throw $this->createNotFoundException(("This outing cannot be deleted"));
        }

        return $this->redirectToRoute('outing_list');
    }

    #[Route('/list', name: 'list')]
    public function list(OutingRepository $outingRepository){
         $outings = $outingRepository->findAll();

        return $this->render('outing/list.html.twig', [
            'outings' => $outings
        ]);
    }
}
