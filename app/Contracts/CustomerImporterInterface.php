<?php

namespace App\Contracts;

interface CustomerImporterInterface
{
    /**
     * Import customers from external source
     *
     * @param int $count Number of customers to import
     * @return void
     */
    public function import(int $count): void;

    /**
     * Handle the import process
     *
     * @param int|null $count Number of customers to import
     * @return void
     */
    public function handle(?int $count = null): void;
}
