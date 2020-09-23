<?php

namespace WooCommerceTimedVouchers\Controllers\Api;

abstract class RestEndpoint
{
    protected $namespace = '/wc/timed-vouchers/v1';

    public function bootstrap(): self
    {
        return new static();
    }

    protected function generate_url(...$appendix): string
    {
        return '/' . implode('/', $appendix);
    }
}
