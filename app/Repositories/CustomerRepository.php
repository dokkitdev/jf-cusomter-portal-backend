<?php

namespace App\Repositories;

use App\Models\Customer;
use Illuminate\Support\Arr;

/**
 * @property Customer $model
*/
class CustomerRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(Customer::class);
    }

    public function filterByNameOrId(): static
    {
        if (Arr::has($this->filter, 'query')) {
            $this->query->where(function ($query) {
                $query->where($this->getQuerySearchCallback('name'));

                if (preg_match('/^\d+/', $this->filter['query'])) {
                    $customerId = (int) $this->filter['query'];

                    $query->orWhere('simpro_customer_id', $customerId);
                }
            });
        }

        return $this;
    }
}
