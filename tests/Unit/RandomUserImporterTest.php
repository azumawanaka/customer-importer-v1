<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Services\RandomUserImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RandomUserImporterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    /**
     * Test import method creates customers with correct data structure.
     */
    public function test_import_creates_customers_with_correct_data(): void
    {
        // Mock HTTP response
        $mockApiResponse = [
            'results' => [
                [
                    'name' => [
                        'first' => 'John',
                        'last' => 'Doe'
                    ],
                    'email' => 'john.doe@example.com',
                    'login' => [
                        'username' => 'johndoe123',
                        'password' => 'plainpassword'
                    ],
                    'gender' => 'male',
                    'phone' => '123-456-7890',
                    'location' => [
                        'city' => 'Sydney',
                        'country' => 'Australia'
                    ]
                ],
                [
                    'name' => [
                        'first' => 'Jane',
                        'last' => 'Smith'
                    ],
                    'email' => 'jane.smith@example.com',
                    'login' => [
                        'username' => 'janesmith456',
                        'password' => 'anotherpassword'
                    ],
                    'gender' => 'female',
                    'phone' => '098-765-4321',
                    'location' => [
                        'city' => 'Melbourne',
                        'country' => 'Australia'
                    ]
                ]
            ]
        ];

        Http::fake([
            'https://randomuser.me/api/*' => Http::response($mockApiResponse, 200)
        ]);

        $importer = new RandomUserImporter();

        $importer->import(2);

        $this->assertDatabaseCount('customers', 2);

        // Verify 1st customer
        $this->assertDatabaseHas('customers', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'username' => 'johndoe123',
            'password' => md5('plainpassword'),
            'gender' => 'male',
            'phone' => '123-456-7890',
            'city' => 'Sydney',
            'country' => 'Australia'
        ]);

        // Verify 2nd customer
        $this->assertDatabaseHas('customers', [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'username' => 'janesmith456',
            'password' => md5('anotherpassword'),
            'gender' => 'female',
            'phone' => '098-765-4321',
            'city' => 'Melbourne',
            'country' => 'Australia'
        ]);
    }

    /**
     * Test import method updates existing customer based on email.
     */
    public function test_import_updates_existing_customer_by_email(): void
    {
        $existingCustomer = Customer::factory()->create([
            'email' => 'john.doe@example.com',
            'first_name' => 'OldJohn',
            'username' => 'oldjohn'
        ]);

        $mockApiResponse = [
            'results' => [
                [
                    'name' => [
                        'first' => 'NewJohn',
                        'last' => 'Doe'
                    ],
                    'email' => 'john.doe@example.com', // Same email
                    'login' => [
                        'username' => 'newjohndoe',
                        'password' => 'newpassword'
                    ],
                    'gender' => 'male',
                    'phone' => '123-456-7890',
                    'location' => [
                        'city' => 'Sydney',
                        'country' => 'Australia'
                    ]
                ]
            ]
        ];

        Http::fake([
            'https://randomuser.me/api/*' => Http::response($mockApiResponse, 200)
        ]);

        $importer = new RandomUserImporter();

        $importer->import(1);

        // Assert - Should still have only 1 customer but with updated data (first_name, username, password)
        $this->assertDatabaseCount('customers', 1);
        $this->assertDatabaseHas('customers', [
            'id' => $existingCustomer->id,
            'email' => 'john.doe@example.com',
            'first_name' => 'NewJohn',
            'username' => 'newjohndoe',
            'password' => md5('newpassword')
        ]);
    }

    /**
     * Test import method handles API failure gracefully.
     */
    public function test_import_handles_api_failure(): void
    {
        Http::fake([
            'https://randomuser.me/api/*' => Http::response([], 500)
        ]);

        $importer = new RandomUserImporter();

        $this->expectException(\Exception::class);
        $importer->import(1);
    }

    /**
     * Test import method sends correct API parameters.
     */
    public function test_import_sends_correct_api_parameters(): void
    {
        // Arrange
        Http::fake([
            'https://randomuser.me/api/*' => Http::response(['results' => []], 200)
        ]);

        $importer = new RandomUserImporter();

        // Act
        $importer->import(5);

        // Assert
        Http::assertSent(function ($request) {
            $url = $request->url();
            $data = $request->data();
            return str_contains($url, 'randomuser.me/api') &&
                   isset($data['results']) && $data['results'] == 5 &&
                   isset($data['nat']) && $data['nat'] === 'AU';
        });
    }

    /**
     * Test handle method uses default count when no count provided.
     */
    public function test_handle_uses_default_count(): void
    {
        Http::fake([
            'https://randomuser.me/api/*' => Http::response(['results' => []], 200)
        ]);

        $importer = new RandomUserImporter('https://randomuser.me/api/', 50);

        $importer->handle();

        Http::assertSent(function ($request) {
            return $request['results'] === 50;
        });
    }

    /**
     * Test handle method uses provided count when given.
     */
    public function test_handle_uses_provided_count(): void
    {
        Http::fake([
            'https://randomuser.me/api/*' => Http::response(['results' => []], 200)
        ]);

        $importer = new RandomUserImporter();

        $importer->handle(25);

        Http::assertSent(function ($request) {
            return $request['results'] === 25;
        });
    }
}
