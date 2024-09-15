<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Entity\Product;
use App\Repository\CartProductRepository;
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
        CartProductRepository  $cartProductRepository,
    ): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_login');
        }

        $message = '';

        // Обработка добавления в корзину (без AJAX)
        if ($request->isMethod('POST')) {
            $productId = $request->request->get('product_id');
            /**@var Product $product */
            $product = $productRepository->find($productId);

            if ($product) {
                // Логика добавления товара в корзину
                $cart = $cartRepository->findOneBy([
                    'isPay' => 0,
                    'userId' => $this->getUser()->getId(),
                ]);
                //$this->debug($user_cart);
                //$this->debug($this->getUser()->getId());
                if (empty($cart)) {
                    $cart = new Cart();
                    $cart->setPay(false);
                    $cart->setUserId($this->getUser()->getId());
                    $entityManager->persist($cart);
                    $entityManager->flush();
                }
                //$this->debug($user_cart);
                //$this->debug($product);

                /**@var CartProduct $cartProduct */
                $cartProduct = $cartProductRepository->findOneBy([
                    'cartId' => $cart->getId(),
                    'productId' => $product->getId(),
                ]);

                if (!empty($cartProduct)) {
                    $cartProduct->setAmount($cartProduct->getAmount() + 1);
                } else {
                    $cartProduct = new CartProduct();
                    $cartProduct->setCartId($cart->getId());
                    $cartProduct->setProductId($product->getId());
                    $cartProduct->setAmount(1);
                }
                $entityManager->persist($cartProduct);
                $entityManager->flush();

                $message = "Товар '{$product->getName()}' добавлен в корзину в количестве - {$cartProduct->getAmount()}";

                //$this->debug($cartProduct);
            }
        }

        $products = $productRepository->findAll();
        return $this->render('main\\index.html.twig', [
            'products' => $products,
            'message' => $message,
        ]);
    }

    public function debug($info)
    {
        echo "<xmp>" . print_r($info, true) . "</xmp>";
    }
}
