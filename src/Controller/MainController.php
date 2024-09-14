<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main', methods: ['GET'])]
    public function index(
        Request           $request,
        ProductRepository $productRepository,
    ): Response
    {
        $products = $productRepository->findAll();
        return $this->render('main\\index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/', name: 'app_add_to_cart', methods: ['POST'])]
    public function add_to_cart(
        Request                $request,
        ProductRepository      $productRepository,
        CartRepository         $cartRepository,
        EntityManagerInterface $entityManager,
    ): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_login');
        }

        // Обработка добавления в корзину (без AJAX)
        if ($request->isMethod('POST')) {
            $productId = $request->request->get('product_id');
            $product = $productRepository->find($productId);

            if ($product) {
                // Логика добавления товара в корзину
                $user_cart = $cartRepository->findBy([
                    'isPay' => 0,
                    'userId' => $this->getUser()->getId(),
                ]);
                //print_r($user_cart);
                //print_r($this->getUser()->getId());
                if (empty($user_cart)) {
                    $user_cart = new Cart();
                    $user_cart->setPay(false);
                    $user_cart->setUserId($this->getUser()->getId());
                    $entityManager->persist($user_cart);
                    $entityManager->flush();
                }
                //print_r($user_cart);

            }
        }

        $products = $productRepository->findAll();
        return $this->render('main\\index.html.twig', [
            'products' => $products,
        ]);
    }
}
