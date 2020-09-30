<?php

namespace WooCommerceTimedVouchers\Models;

if (!class_exists('\WC_Product')) {
    class WC_Product_Timed_Voucher {
        public const TYPE = 'timed-voucher';
    }
    return;
}

class WC_Product_Timed_Voucher extends \WC_Product
{
    public const TYPE = 'timed-voucher';
    public const M_K_ROUTE = '_tv_routes';
    public const M_K_STARTS_ON_OPEN = '_tv_times_on_first_open';
    public const M_K_TIME_AVAILABLE = '_tv_time_seconds_open';

    public const M_D_STARTS_ON_OPEN = true;
    public const M_D_TIME_AVAILABLE = 3600 * 48; // Defaults to 48 hours

    public const F_K_STORE_ROUTES_ROUTES = 'TimedVouchers/Model/Routes/store';

    public function get_type(): string
    {
        return static::TYPE;
    }

    public function add_to_cart_url()
    {
        $url = ($this->is_purchasable() && $this->is_in_stock()) ?
            remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->id ) ) :
            get_permalink( $this->id );


        return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
    }

    public function available_for()
    {
        $meta = $this->get_meta(static::M_K_TIME_AVAILABLE);
        if ($meta) {
            return $meta;
        }

        return static::M_D_TIME_AVAILABLE;
    }

    public function starts_on_open(): bool
    {
        return filter_var(
            $this->get_meta(static::M_K_STARTS_ON_OPEN) || static::M_D_STARTS_ON_OPEN,
            FILTER_VALIDATE_BOOLEAN
        );
    }

    public function get_routes_as_posts(): array
    {
        return array_map(static function ($id) {
            return get_post($id);
        }, $this->get_routes());
    }

    public function get_routes(): array
    {
        $childen = $this->get_meta(static::M_K_ROUTE);

        if (!$childen) {
            return [];
        }

        return $childen;
    }

    /**
     * @param int ...$routes
     */
    public function set_routes(...$routes): void
    {
        $data = apply_filters(static::F_K_STORE_ROUTES_ROUTES, $routes, $this->get_id(), $this);
        $this->update_meta_data(static::M_K_ROUTE, $data);
        $this->save_meta_data();
    }
}
