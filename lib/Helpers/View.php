<?php

namespace WooCommerceTimedVouchers\Helpers;

use Jenssegers\Blade\Blade;

class View
{
    /**
     * @var null|Blade
     */
    public static $_blade = null;

    protected static function bootstrap(): Blade
    {
        if (null === static::$_blade) {
            static::$_blade = new Blade(WOOTV_DIR . '/views', WOOTV_DIR . '/cache');
        }

        do_action('TimedVouchers/Helpers/Views/directives', static::$_blade);

        return static::$_blade;
    }

    public static function make(string $view, array $data = [], array $mergeData = []): \Illuminate\Contracts\View\View
    {
        return self::bootstrap()->make(...func_get_args());
    }

    public static function render(string $view, array $data = [], array $mergeData = []): string
    {
        return static::make(...func_get_args())->render();
    }
}
