<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\Http;

class RandomUserImporter
{
    protected $apiUrl;
    protected $defaultCount;

    /**
     * Create a new service instance.
     *
     * @param string $apiUrl The API URL for fetching random users.
     * @param int $defaultCount Default number of users to import.
     */
    public function __construct($apiUrl = "https://randomuser.me/api/", $defaultCount = 100)
    {
        $this->apiUrl = $apiUrl;
        $this->defaultCount = $defaultCount;
    }

    /**
     * Handle the service logic.
     *
     * @param int|null $count Number of users to import (optional).
     */
    public function handle($count = null)
    {
        $this->import($count ?? $this->defaultCount);
    }

    /**
     * Import random users and save them to the database.
     *
     * @param int $count Number of users to import.
     */
    public function import($count)
    {
        $response = Http::get($this->apiUrl, [
            'results' => $count,
            'nat' => 'AU'
        ]);

        $users = $response->json('results');

        foreach ($users as $user) {
            Customer::updateOrCreate(
                ['email' => $user['email']],
                [
                    'first_name' => $user['name']['first'],
                    'last_name' => $user['name']['last'],
                    'username' => $user['login']['username'],
                    'password' => md5($user['login']['password']),
                    'gender' => $user['gender'],
                    'phone' => $user['phone'],
                    'city' => $user['location']['city'],
                    'country' => $user['location']['country'],
                ]
            );
        }
    }
}