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

    public function filterByUserGroups()
    {
        if (Arr::has($this->filter, 'customer_has_user')) {
            $this->query->whereHas('groups.users', function ($query) {
                $query->where('user_id', $this->filter['customer_has_user']);
            });
        }

        return $this;
    }

    public function filterByNameOrId()
    {
        if (Arr::has($this->filter, 'query')) {
            $this->query->where(function ($query) {
                $query->where($this->getQuerySearchCallback('name'));

                if (preg_match('/^\d+/', $this->filter['query'])) {
                    $customerId = (int) $this->filter['query'];

                    $query->orWhere('customer_id', $customerId);
                }
            });
        }

        return $this;
    }
}
