<?php

namespace App\Controller;

use App\Classe\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{

    #[Route('/my-cart', name: 'app_cart')]
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getTotal()

        ]);
    }

    #[Route('/account/cart/add/{id}', name: 'app_add_to_cart')]
    public function add(Cart $cart,int $id): Response
    {
        $cart->addToCart($id);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/account/cart/removeAll', name: 'app_remove_all_cart')]
    public function removeAll(Cart $cart): Response
    {
        $cart->removeCartAll();
        return $this->redirectToRoute('app_products');
    }

    #[Route('/account/cart/remove/{id}', name: 'app_remove_to_cart')]
    public function removeOneToCart(Cart $cart,int $id): Response
    {
        $cart->removeOneToCart($id);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/account/my-cart/decrease/{id}', name: 'app_cart_decrease')]
    public function decrease(Cart $cart,int $id): RedirectResponse
    {
        $cart->decrease($id);
        return $this->redirectToRoute('app_cart');
    }


}
