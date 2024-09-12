<?php

// src/Entity/PaymentService.php

namespace AppEntity;

use DoctrineORMMapping as ORM;

/**
 * @ORMEntity()
 * @ORMTable(name="payment_services")
 */
class PaymentService
{
    /**
     * @ORMId
     * @ORMGeneratedValue
     * @ORMColumn(type="integer")
     */
    private $id;

    /**
     * @ORMColumn(type="string", length=255)
     */
    private $name;

    // Геттеры и сеттеры...
}
