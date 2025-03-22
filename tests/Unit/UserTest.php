<?php

namespace Tests\Unit;

use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     */

    /** @test */
    public function get_customer_by_id()
    {
        $customers = [
            ['id' => 1, 'name' => 'Budi'],
            ['id' => 2, 'name' => 'Siti'],
        ];

        $customer = collect($customers)->firstWhere('id', 1);

        $this->assertEquals('Budi', $customer['name']);
    }

    /** @test */
    public function get_customer_transactions()
    {
        $transactions = [
            ['customer_id' => 1, 'total' => 50000],
            ['customer_id' => 1, 'total' => 100000],
        ];

        $customerTransactions = collect($transactions)->where('customer_id', 1)->all();

        $this->assertCount(2, $customerTransactions);
    }
}
