<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $products = $this->entityManager->getRepository(Product::class)->findByIsBest(1);

        return $this->render('home/index.html.twig', [
                'products' => $products
            ]
        );
    }
    #[Route('/feature', name: 'app_feature')]
    public function payement(): Response
    {
        //return $this->redirectToRoute('app_order_payments');
        return $this->render('order/payement.html.twig');
    }
}
