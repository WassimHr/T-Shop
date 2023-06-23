<?php

namespace App\Service;


use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private $repo;

    private $rs;

    //injection de dépendances hors d'un controller : constructeur
    public function __construct(ProduitRepository $repo, RequestStack $rs)
    {
        $this->rs = $rs;
        $this->repo = $repo;
    }

    public function add($id)
    {
        //Nous récuperons ou créons une session grâce à la class RequestStack (service)
        $session = $this->rs->getSession();

        $cart = $session->get('cart', []);
        $qt = $session->get('qt', 0);
        //je récupere l'attribut de session 'cart' s'il existe ou un tableau vide
        if(!empty($cart[$id]))
        {
            $cart[$id]++;
            $qt++;
        }else{
            $cart[$id] = 1;
            $qt++;
        }
        
        //dans mon tableau $cart, à la case $id, je donne la valeur 1
        $session->set('qt', $qt);
        $session->set('cart', $cart);
    }

    public function remove($id)
    {
        $session = $this->rs->getSession();
        $cart = $session->get('cart', []);
        $qt = $session->get('qt', 0);

        if(!empty($cart[$id]))
        {
            $qt -= $cart[$id];
            unset($cart[$id]);
        }
        if($qt < 0)
        {
            $qt = 0;
        }
        $session->set('qt', $qt);
        $session->set('cart', $cart);
    }

    public function cartWithData()
    {
        $session = $this->rs->getSession();
        $cart = $session->get('cart', []);

        //je vais créer un nouveau tableau qui contiendra des objets Product et les quantité de chaque objet
        $cartWithData = [];
        $total = 0;

        //Pour chaque $id qui se trouve dans le tableau $cart, j'ajoute une case au tableau $cartWithData
        //dans chacune de ces il y aura mon tableau associatif contenant 2 case: une pour product et une pour quantity
        foreach($cart as $id => $quantity)
        {

            $produit = $this->repo->find($id);
            $cartWithData[]= [
                'produit' => $produit,
                'quantite' => $quantity
            ];
        }
        return $cartWithData;
    }

    public function total()
    {
        $cartWithData = $this->cartWithData();
        $total = 0;

        foreach($cartWithData as $item)
        {
            $totalItem = $item['produit']->getPrix() * $item['quantite'];
            $total += $totalItem;
        }

        return $total;
    }
}
