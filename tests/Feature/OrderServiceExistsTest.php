<?php

namespace Tests\Feature;

use App\Services\OrderService;
use Tests\TestCase;

class OrderServiceExistsTest extends TestCase
{
    public function test_order_service_class_exists(): void
    {
        $this->assertTrue(class_exists(OrderService::class));
    }
}
