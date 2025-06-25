<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    /**
     * Test GET /customers returns list of customers with correct structure.
     */
    public function test_index_returns_customers_list_with_correct_structure(): void
    {
        $customers = Customer::factory()->count(3)->create();

        $response = $this->getJson('/api/customers');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'full_name',
                        'email',
                        'country'
                    ]
                ]
            ])
            ->assertJson([
                'success' => true
            ]);

        $responseData = $response->json('data');
        $this->assertCount(3, $responseData);

        // Verify full name is concatenated correctly
        foreach ($responseData as $index => $customerData) {
            $customer = $customers[$index];
            $expectedFullName = $customer->first_name . ' ' . $customer->last_name;
            $this->assertEquals($expectedFullName, $customerData['full_name']);
            $this->assertEquals($customer->email, $customerData['email']);
            $this->assertEquals($customer->country, $customerData['country']);
        }
    }

    /**
     * Test GET /customers returns empty array when no customers exist.
     */
    public function test_index_returns_empty_array_when_no_customers(): void
    {
        $response = $this->getJson('/api/customers');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => []
            ]);
    }

    /**
     * Test GET /customers/{id} returns customer details with correct structure.
     */
    public function test_show_returns_customer_details_with_correct_structure(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->getJson("/api/customers/{$customer->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'full_name',
                    'email',
                    'username',
                    'gender',
                    'country',
                    'city',
                    'phone'
                ]
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'full_name' => $customer->first_name . ' ' . $customer->last_name,
                    'email' => $customer->email,
                    'username' => $customer->username,
                    'gender' => $customer->gender,
                    'country' => $customer->country,
                    'city' => $customer->city,
                    'phone' => $customer->phone
                ]
            ]);
    }

    /**
     * Test GET /customers/{id} returns 404 for non-existent customer.
     */
    public function test_show_returns_404_for_non_existent_customer(): void
    {
        $response = $this->getJson('/api/customers/999');

        $response->assertStatus(404)
            ->assertJsonStructure([
                'success',
                'message'
            ])
            ->assertJson([
                'success' => false,
                'message' => 'Customer not found'
            ]);
    }

    /**
     * Test GET /customers/{id} handles invalid ID format gracefully.
     */
    public function test_show_handles_invalid_id_format(): void
    {
        $response = $this->getJson('/api/customers/invalid-id');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Customer not found'
            ]);
    }
}
