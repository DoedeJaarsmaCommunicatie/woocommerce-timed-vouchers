@php
/**
 * @var \WooCommerceTimedVouchers\DBO\Secret $secret
 */
@endphp
<p>
    <strong>Code:</strong><em>{{ $secret->secret }}</em>
</p>
<p>
    <strong>Geldigheid: </strong><em>
        @if($secret->is_valid())
            Geldig
        @else
            Ongeldig (gebruikt)
        @endif
    </em>
</p>
<p>
    <strong>Geldig tot:</strong><em>
        @if($secret->valid_until)
            {{ \Carbon\Carbon::make($secret->valid_until)->format('d-m-Y H:m') }}
        @else
            Niet gebruikt
        @endif
    </em>
</p>

@php
/**
 * @var \WooCommerceTimedVouchers\DBO\Secret $secret
 */
do_action('TimedVouchers/Views/secret-mail-meta/post-code', $secret->product_id, $secret->order_id, $secret)
@endphp
