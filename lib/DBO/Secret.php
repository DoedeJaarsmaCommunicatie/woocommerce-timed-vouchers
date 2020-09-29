<?php

namespace WooCommerceTimedVouchers\DBO;

use Carbon\Carbon;
use WooCommerceTimedVouchers\Models\WC_Product_Timed_Voucher;

/**
 * Class Secret
 * @package WooCommerceTimedVouchers\DBO
 *
 * @property int $id
 * @property int $order_id
 * @property \DateTime $created_at
 * @property string $secret
 * @property \DateTime|null $valid_until
 * @property  int $product_id
 *
 * @method static Secret make($data = [])
 *
 * @method Secret find($secret)
 * @method Secret findOn($column, $value)
 * @method Secret[] map($data)
 * @method Secret[] findManyOn($column, $operator, $value = null)
 *
 * @mixin Model
 */
class Secret extends Model
{
    protected $table_name = 'secret_order_keys';
    protected $key = 'secret';

    protected $casts = [
        'order_id' => 'int',
        'product_id' => 'int'
    ];

    public function is_valid(): bool
    {
        if (!$this->valid_until) {
            return true;
        }

        $valid_until = Carbon::make($this->valid_until);
        return $valid_until? !$valid_until->isPast() : true;
    }

    public function start_routes(): void
    {
        if (null === $this->valid_until) {
            $this->valid_until = Carbon::now()->addSeconds(WC_Product_Timed_Voucher::M_D_TIME_AVAILABLE);
            $this->patch();
        }
    }

    public function has_order(): bool
    {
        return $this->order_id !== -1;
    }

    public function order()
    {
        if (!$this->has_order()) {
            return false;
        }

        return wc_get_order($this->order_id);
    }

    public function product()
    {
        if (!$this->product_id) {
            return false;
        }

        return wc_get_product($this->product_id);
    }
}
