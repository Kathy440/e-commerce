<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/order', name: 'app_order')]
    public function index(Cart $cart, Request $request): Response
    {
        if (!$this->getUser()->getAddresses()->getValues()) {
            return $this->redirectToRoute('app_add_address');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);


        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getTotal()
        ]);
    }

    #[Route('/order/recap', name: 'app_order_recap', methods: ["POST"])]
    public function add(Cart $cart, Request $request): Response
    {

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTimeImmutable();
            $carries = $form->get('carries')->getData();

            $delivery = $form->get('addresses')->getData();

            $delivery_content = $delivery->getFirstName() . ' ' . $delivery->getLastName();
            $delivery_content .= '<br/>' . $delivery->getPhone();


            if ($delivery->getCompagny()) {
                $delivery_content .= '<br/>' . $delivery->getCompagny();
            }

            $delivery_content .= '<br/>' . $delivery->getAddress();
            $delivery_content .= '<br/>' . $delivery->getPostal() . ' ' . $delivery->getCity();
            $delivery_content .= '<br/>' . $delivery->getCountry();

            // save commande OrderEntity()
            $order = new Order();
            //recup user
            $order->setUser($this->getUser());
            $order->setCreateAt($date);
            $order->setCarrierName($carries->getName());
            $order->setCarrierPrice($carries->getPrice());
            $order->setDelivery($delivery_content);
            $order->setIsPaid(0);

            $this->entityManager->persist($order);


            //Stripe Config en cours ...
           /* $lineItems = array();
            $YOUR_DOMAIN = 'http://127.0.0.1:8000/';*/

            // save produits ProductDetailEntity()
            foreach ($cart->getTotal() as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);

                $this->entityManager->persist($orderDetails);


                //STRIPE CONFIG en cours
               /* $lineItems[] = array(
                    'price_data' => array(
                        'currency' => 'eur',
                        'unit_amount' => $product['product']->getPrice(),
                        'product_data' => array(
                            'name' => $product['product']->getName(),
                            'images' => $product['product']->getIllustration()
                        )
                    ),
                    'quantity' => $product['quantity'],
                )
                ;*/
            }


            $this->entityManager->flush();

            //STRIPE Config en cours
            /*$stripeSecretKey = 'sk_test_51MY8AyB7rpMplmxJQ0KWQgD1r5bPexedGb73J736hPQVbGNfbOEmIuHQfkcJOJPlATuyTHVQnt4PA5YgnRdqy2b80040QCT8y7';
            Stripe::setApiKey($stripeSecretKey);
            $checkout_session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => $YOUR_DOMAIN . '/success.html',
                'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
            ]);*/
            //dump($checkout_session->id);
            //dd($checkout_session);



            return $this->render('order/add.html.twig', [
                'cart' => $cart->getTotal(),
                'carrier' => $carries,
                'delivery' => $delivery_content
            ]);
        }
        return $this->redirectToRoute('app_cart');
    }

}
