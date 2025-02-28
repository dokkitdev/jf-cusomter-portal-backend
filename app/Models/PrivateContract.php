<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class PrivateContract extends Model
{
    use ModelTrait;

    const PAYMENT_TYPE_ANNUAL_PAYMENT = 'Annual payment';
    const PAYMENT_TYPE_DIRECT_DEBIT = 'Direct Debit';

    const TEMPLATE_TYPE_ANNUAL_PAYMENT = 'annual_payment';
    const TEMPLATE_TYPE_DIRECT_DEBIT = 'direct_debit';

    const PAYMENT_TYPES = [
        self::PAYMENT_TYPE_ANNUAL_PAYMENT,
        self::PAYMENT_TYPE_DIRECT_DEBIT,
    ];

    const TEMPLATE_TYPES = [
        self::TEMPLATE_TYPE_ANNUAL_PAYMENT,
        self::TEMPLATE_TYPE_DIRECT_DEBIT,
    ];

    const CF_PAYMENT_TYPE_ID = 4;
    const CF_PERIOD_ID = 3;
    const CF_PAYER_REFERENCE_ID = 6;
    const CF_DIRECT_DATE_ID = 5;
    const CF_PAYER_ACCOUNT_NAME_ID = 7;
    const CF_DIRECT_MONTH_ID = 8;

    protected $fillable = [
        'direct_date',
        'direct_month',
        'simpro_recurring_invoice_id',
        'customer_id',
        'site_id',
        'recurring_type',
        'company_name',
        'period',
        'payer_reference',
        'payer_account_name',
        'payment_type',
        'is_company',
        'is_processed',
        'next_recurring_date',
    ];

    protected $hidden = ['pivot'];

    protected $casts = [
        'is_company' => 'boolean',
        'is_processed' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function cost_centers()
    {
        return $this->hasMany(PrivateContractCostCenter::class, 'private_contract_id', 'id');
    }
}
