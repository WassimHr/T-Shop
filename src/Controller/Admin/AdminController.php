<?php

namespace App\Controller\Admin;

use App\Entity\Membre;
use App\Entity\Produit;
use App\Entity\Commande;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class AdminController extends AbstractDashboardController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
        {

        }
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {

        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
        $url = $this->adminUrlGenerator->setController(MembreCrudController::class)->generateUrl();
        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('My T-Shop');
    }

    public function configureMenuItems(): iterable
    {
        // yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
        return [
            MenuItem::linkToDashboard("BACKOFFICE", 'fa fa-home'),
            MenuItem::section('Membre'),
            MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', Membre::class),
            MenuItem::section('Produit'),
            MenuItem::linkToCrud('Produits', 'fa-solid fa-shirt', Produit::class),
            MenuItem::section('Commande'),
            MenuItem::linkToCrud('Commandes', 'fa-solid fa-cart-shopping', Commande::class),
            MenuItem::section('Retour au site'),
            MenuItem::linkToRoute('Accueil du site', 'fa fa-igloo', 'app'),
        ];

    }
}
