<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{
    #[Route('/', name: 'app')]
    public function index(ProduitRepository $repo): Response
    {
        $produit = $repo->findAll();
        return $this->render('app/index.html.twig', [
            'produit' => $produit
        ]);
    }


    #[Route('/show/{id}', name: 'show')]
    public function show(Produit $produit=null): Response
    {
        if($produit == null)
        {
            return $this->redirectToRoute('app');
        }
        return $this->render('app/show.html.twig', [
            'produit' => $produit
        ]);
    }


   

}
