<?php

namespace App\Controller;

use App\Entity\Commande;

use App\Service\CartService;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(ProduitRepository $repo, RequestStack $rs, CartService $cs): Response
    {
        $cartWithData = $cs->cartWithData();
        $total = $cs->total();

        return $this->render('cart/index.html.twig',[
            'items' => $cartWithData,
            'total' => $total
        ]);
    }

    #[Route('/cart/add/{id}', name:'cart_add')]
    public function add($id, CartService $cs)
    {
        $cs->add($id);
        return $this->redirectToRoute('app');
    }


    #[Route('/cart/remove/{id}', name:'cart_remove')]
    public function remove($id, CartService $cs)
    {
        $cs->remove($id);

        return $this->redirectToRoute('app_cart');
    }


    #[Route('/cart/commande/', name: 'cart_commande')]
    public function cartCommande(EntityManagerInterface $manager, Request $request, Commande $commande = null, CartService $cs, ProduitRepository $repo): Response
    {
        $cartWithData = $cs->cartWithData();
        $total = $cs->total();
        $errors = [];
        $produitsMisAJour = [];
    
        foreach ($cartWithData as $item) {
            $produit = $repo->find($item['produit']->getId());
            $quantiteCommandee = $item['quantite'];
    
            $montant = $produit->getPrix() * $quantiteCommandee;
    
            $commande = new Commande();
            $commande
                ->setMembre($this->getUser())
                ->setProduit($produit)
                ->setQuantite($quantiteCommandee)
                ->setMontant($montant)
                ->setEtat('En traitement')
                ->setDateEnregistrement(new \DateTime());
    
            $produit->setStock($produit->getStock() - $quantiteCommandee);
            $produitsMisAJour[] = $produit;
    
            $manager->persist($commande);
        }
    
        foreach ($produitsMisAJour as $produitMisAJour) {
            $manager->persist($produitMisAJour);
        }
    
        $manager->flush();
    
        // $cs->clearCart();
    
        return $this->redirectToRoute('cart_commandes_user');
    }
    
    
    #[Route('/cart/commandes', name: 'cart_commandes_user')]
        public function commandesUtilisateur(): Response
        {
            
    
            return $this->render('commandes/index.html.twig');
        }
    
        #[Route('/cart/commande/{id}/delete', name: 'cart_commande_delete')]
    public function deleteCommande(EntityManagerInterface $manager, Commande $commande = null, ProduitRepository $produitRepository): Response
    {
        if (!$commande) {
            throw $this->createNotFoundException('La commande n\'existe pas.');
        }
    
        $produit = $commande->getProduit();
        $quantiteCommandee = $commande->getQuantite();
    
        // Ajouter la quantité commandée au stock du produit
        $produit->setStock($produit->getStock() + $quantiteCommandee);
    
        $manager->remove($commande);
        $manager->persist($produit);
        $manager->flush();
    
        return $this->redirectToRoute('cart_commandes_user');
    }
    
    #[Route('/cart/commandes', name: 'commandes_user')]
    public function afficherCommandesUtilisateur(): Response
    {
       
        return $this->render('commandes/index.html.twig');
    }
    
}