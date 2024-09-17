<?php
// tests/Controller/MainControllerTest.php
namespace App\Tests\Controller;

use App\Controller\MainController;
use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Entity\Product;
use App\Entity\DeliveryService;
use App\Entity\PaymentService;
use App\Repository\CartProductRepository;
use App\Repository\CartRepository;
use App\Repository\DeliveryServiceRepository;
use App\Repository\PaymentServiceRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    private MainController $controller;

    // Создание моков для зависимостей
    private $entityManager;
    private $cartRepository;
    private $cartProductRepository;
    private $productRepository;
    private $deliveryServiceRepository;
    private $paymentServiceRepository;

    protected function setUp(): void
    {
        // Инициализация ядра
        self::bootKernel();

        // Создание мока EntityManagerInterface
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        // Создание моков для репозиториев
        $this->cartRepository = $this->createMock(CartRepository::class);
        $this->cartProductRepository = $this->createMock(CartProductRepository::class);
        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->deliveryServiceRepository = $this->createMock(DeliveryServiceRepository::class);
        $this->paymentServiceRepository = $this->createMock(PaymentServiceRepository::class);

        // Инициализация контроллера с моками всех зависимостей
        $this->controller = new MainController(
            $this->entityManager,
            $this->cartRepository,
            $this->cartProductRepository,
            $this->deliveryServiceRepository,
            $this->paymentServiceRepository,
            $this->createMock(\Symfony\Contracts\HttpClient\HttpClientInterface::class)
        );
    }

    public function testSomething1()
    {
        $this->assertStringContainsString("1", "1");
    }

}
