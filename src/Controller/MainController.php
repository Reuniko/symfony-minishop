<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        // Обработка добавления в корзину (без AJAX)
        if ($request->isMethod('POST')) {
            $productId = $request->request->get('product_id');
            $product = $productRepository->find($productId);

            if ($product) {
                // Логика добавления товара в корзину
                // ...
            }
        }

        return $this->render('main\\index.html.twig', [
            'products' => $products,
        ]);
    }
}
