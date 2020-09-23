@php
/**
 * @var \WooCommerceTimedVouchers\DBO\Secret $secret
 */
@endphp
<p>
    <strong>Code: </strong><em>{{ $secret->secret }}</em>

    @php
    /**
     * @var \WooCommerceTimedVouchers\DBO\Secret $secret
     */
        do_action('TimedVouchers/Views/secret-mail-meta/post-code', $secret->product_id, $secret->order_id, $secret)
    @endphp
</p>
