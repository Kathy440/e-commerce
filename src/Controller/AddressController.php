<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddressController extends AbstractController
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/account/address', name: 'app_address')]
    public function index(): Response
    {
        return $this->render('account/address.html.twig');
    }

    #[Route('/account/add_address', name: 'app_add_address')]
    public function addAddress(Cart $cart, Request $request): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());
            $this->entityManager->persist($address);
            $this->entityManager->flush();
            if ($cart->getSession()) {
                return $this->redirectToRoute('app_order');
            } else {
                return $this->redirectToRoute('app_address');
            }
        }

        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/account/edit_address/{id}', name: 'app_edit_address')]
    public function editAddress(Request $request, int $id): Response
    {
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);

        if (!$address || $address->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_address');
        }

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('app_address');
        }

        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/account/delete_address/{id}', name: 'app_delete_address')]
    public function deleteAddress(int $id): Response
    {
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);

        if ($address && $address->getUser() == $this->getUser()) {
            $this->entityManager->remove($address);
            $this->entityManager->flush();
        }


        return $this->redirectToRoute('app_address');
    }
}
