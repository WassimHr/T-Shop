<?php

namespace App\Controller;

use App\Service\CartService;

use App\Repository\ProduitRepository;
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
}