<?php

namespace AppEntity;

use DoctrineORMMapping as ORM;

/**
 * @ORMEntity()
 * @ORMTable(name="delivery_services")
 */
class DeliveryService
{
    /**
     * @ORMId
     * @ORMGeneratedValue
     * @ORMColumn(type="integer")
     */
    private $id;

    /**
     * @ORMColumn(type="string", length=50)
     */
    private $code;

    /**
     * @ORMColumn(type="string", length=255)
     */
    private $name;

    // Геттеры и сеттеры...
}
