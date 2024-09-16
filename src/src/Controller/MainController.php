<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Entity\Product;
use App\Repository\CartProductRepository;
use App\Repository\CartRepository;
use App\Repository\DeliveryServiceRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class MainController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface    $entityManager,
        private CartRepository            $cartRepository,
        private CartProductRepository     $cartProductRepository,
        private DeliveryServiceRepository $deliveryServiceRepository,
        private HttpClientInterface       $httpClient,
    )
    {

    }

    private function getUserCart(): Cart
    {
        $cart = $this->cartRepository->findOneBy([
            'isPay' => 0,
            'userId' => $this->getUser()->getId(),
        ]);
        if (empty($cart)) {
            $cart = new Cart();
            $cart->setPay(false);
            $cart->setUserId($this->getUser()->getId());
            $this->entityManager->persist($cart);
            $this->entityManager->flush();
        }
        return $cart;
    }

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

                $cart = $this->getUserCart();

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

    /**
     * Доступные способы отладки ($mode):
     * - textarea - невидимая textarea, отображается по нажатию клавиш alt + TILDE (по умолчанию)
     * - console - выводится в консоль браузера
     * - log - записывается в файл
     * - email - отправляется на почту
     *
     * @param mixed $data переменная для отладки
     * @param string $title название переменной (не обязательно)
     * @param string $mode способ отладки (не обязательно)
     * @param string $target путь отправки: имя файла или почтовый адрес (не обязательно)
     */
    public function debug($data = [], $title = '', $mode = 'textarea', $target = '/debug.txt')
    {
        if ($mode === 'textarea') {
            echo "<textarea class='debug' data-debug='{$title}' style='
				display: none; 
				resize: both; 
				position: relative; 
				z-index: 99999; 
				border: 1px green dashed;
				width: auto;
				'
			>{$title}=" . htmlspecialchars(print_r($data, true)) . "</textarea>";
            static $need_js = true;
            if ($need_js) {
                $need_js = false;
                ?>
                <script>
                    (function () {
                        if (typeof NodeList.prototype.forEach === "function") return false;
                        NodeList.prototype.forEach = Array.prototype.forEach;
                    })();
                    if (!window.fls_debug) {
                        document.addEventListener('keydown', function (event) {
                            // alt + TILDE
                            if ((event.altKey || event.ctrlKey) && event.keyCode === 192) {
                                var debug = document.querySelectorAll('.debug');
                                debug.forEach(function (element) {
                                    element.style.display = (element.style.display == 'none') ? 'block' : 'none';
                                });
                            }
                        });
                        window.fls_debug = true;
                    }
                </script>
                <?php
            }
        }
        if ($mode === 'console') {
            echo "<script>console.log('{$title}', " . json_encode($data, true) . ");</script>";
        }
        if ($mode === 'log') {
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . $target, "\r\n" . str_repeat('-', 50) . "\r\n" . $title . '=' . print_r($data, true), FILE_APPEND);
        }
        if ($mode === 'email') {
            mail($target, "debug from {$_SERVER['SERVER_NAME']}", $title . '=' . print_r($data, true));
        }
    }

    #[Route('/checkout', name: 'app_cart', methods: ['GET'])]
    public function app_cart(
        Request                $request,
        ProductRepository      $productRepository,
        CartRepository         $cartRepository,
        EntityManagerInterface $entityManager,
        CartProductRepository  $cartProductRepository,
    ): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_main');
        }

        $message = '';

        $cart = $this->getUserCart();
        $cartProducts = $this->getCartProducts($cart->getId());

        //$this->debug($cart);
        $this->debug($cartProducts);

        $totalPrice = 0;
        foreach ($cartProducts as $cartProduct) {
            $totalPrice += $cartProduct['product_price'] * $cartProduct['amount'];
        }

        return $this->render('main\\cart.html.twig', [
            'cart' => $cart,
            'cartProducts' => $cartProducts,
            'message' => $message,
            'totalPrice' => $totalPrice,
        ]);
    }

    private function getCartProducts(int $cartId): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select('
                cp.id AS cart_product_id,
                cp.amount,
                p.id AS product_id,
                p.name AS product_name,
                p.price AS product_price,
                p.weight AS product_weight
            ')
            ->from('App\Entity\CartProduct', 'cp')
            //->leftJoin('cp.product', 'p') // no idea
            ->leftJoin(
                'App\Entity\Product',
                'p',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'cp.productId = p.id'
            )
            ->where('cp.cartId = :cartId')
            ->setParameter('cartId', $cartId);

        $result = $queryBuilder->getQuery()->getResult();

        return $result;
    }

    #[Route('/checkout/delete/{cartProductId}', name: 'app_cart_delete', methods: ['POST'])]
    public function deleteCartItem(int $cartProductId): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_main');
        }
        try {
            $cartProduct = $this->cartProductRepository->find($cartProductId);
            if (!$cartProduct) {
                throw new \Exception("Позиция в корзине не найдена");
            }
            $cart = $this->cartRepository->findOneBy([
                'userId' => $this->getUser()->getId(),
                'isPay' => 0,
            ]);
            if (empty($cart)) {
                throw new \Exception("Корзина не найдена");
            }
            $this->entityManager->remove($cartProduct);
            $this->entityManager->flush();
        } catch (\Exception $error) {
            return $this->redirectToRoute('app_cart', ['error' => $error->getMessage()]);
        }
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/checkout/delivery/', name: 'app_cart_delivery')]
    public function app_cart_delivery(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_main');
        }

        $errors = [];

        $referrer = $request->headers->get('referer');
        if (!$referrer || !str_contains($referrer, '/checkout')) {
            return $this->redirectToRoute('app_cart');
        }

        $deliveryServices = $this->deliveryServiceRepository->findAll();

        foreach ($deliveryServices as $deliveryService) {
            $apiEndpoint = "http://localhost:8888/delivery/{$deliveryService->getCode()}";
            $requestData = [];
            $headers = ['Content-Type' => 'application/json'];
            switch ($deliveryService->getCode()) {
                case 'cdek':
                    $requestData = [
                        'username' => 'cdek-user-01',
                        'password' => '123456789',
                        'weight' => '5',
                    ];
                    break;
                case 'fivepost':
                    $headers['apiKey'] = '448ed7416fce2cb66c285d182b1ba3df1e90016d';
                    $requestData = [
                        'weight' => '50',
                    ];
                    break;
            }

            try {
                $response = $this->httpClient->request(
                    'POST',
                    $apiEndpoint,
                    [
                        'headers' => $headers,
                        'body' => json_encode($requestData),
                        'timeout' => 1,
                    ]
                );

                $responseData = json_decode($response->getContent(), true);

                if ($responseData['status'] === true || $responseData['status'] === 200) {
                    $deliveryService->price = $responseData['data']['price'];
                    $deliveryService->minDays = $responseData['data']['delivery_min_days'];
                    $deliveryService->maxDays = $responseData['data']['delivery_max_days'];
                } else {
                    throw new \Exception("Статус ответа - {$responseData['status']}");
                }
            } catch (\Exception $error) {
                $deliveryService->price = '???';
                $deliveryService->minDays = '???';
                $deliveryService->maxDays = '???';
                $errors[] = 'Не удалось получить данные о доставке от ' . $deliveryService->getName() . ': ' . $error->getMessage();
            }
        }

        $this->debug($deliveryServices, '$deliveryServices');

        return $this->render('main/delivery.html.twig', [
            'deliveryServices' => $deliveryServices,
            'errors' => $errors,
        ]);
    }
}
