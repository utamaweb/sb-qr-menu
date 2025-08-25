<li id="daily-sale-outlet">
    @php
        $params = [
            'year' => date('Y'),
            'month' => date('m'),
            'warehouse_id' => auth()->user()->warehouse_id
        ];
        $hashParams = encrypt(json_encode($params));
    @endphp
    <a href="{{ route('admin.report.daily_sale_outlet') }}?hash={{ $hashParams }}">
        <i class="dripicons-document-remove"></i>
        <span>Laporan Sales</span>
    </a>
</li>
