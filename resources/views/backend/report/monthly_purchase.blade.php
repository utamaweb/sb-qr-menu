@extends('backend.layout.main') @section('content')
<section>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h4 class="text-center">{{trans('file.Monthly Purchase Report')}} &nbsp;&nbsp;</h4>

                @if(auth()->user()->hasRole(['Admin Bisnis', 'Report']))
                <div class="row mb-4">
                    <div class="col-md-6 offset-md-3 text-center">
                        <form action="{{ url("admin/report/monthly_purchase/$year") }}" method="GET">
                            <div class="form-group mb-3">
                                <select name="warehouse_id" id="warehouse_id" class="form-control selectpicker" data-live-search="true">
                                    <option value="">Pilih Outlet</option>
                                    @foreach($warehouses as $warehouse_item)
                                        <option value="{{ $warehouse_item->id }}" {{ ($warehouse_id == $warehouse_item->id) ? 'selected' : '' }}>
                                            {{ $warehouse_item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary px-4">Filter</button>
                        </form>
                    </div>
                </div>
                @endif

                <!-- Show current warehouse information -->
                @if(isset($warehouse) && $warehouse)
                    <h5 class="text-center mb-3">Outlet: {{ $warehouse->name }}</h5>
                @elseif($warehouse_id == '' && auth()->user()->hasRole(['Admin Bisnis', 'Report']))
                    <h5 class="text-center mb-3">Outlet Belum Dipilih</h5>
                @elseif(!auth()->user()->hasRole(['Admin Bisnis', 'Report']))
                    <h5 class="text-center mb-3">Outlet: {{ auth()->user()->warehouse->name ?? 'Tidak Ditemukan' }}</h5>
                @endif

                <div class="table-responsive mt-4">
                    <table class="table table-bordered" style="border-top: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                        <thead>
                            <tr>
                                <th><a href="{{url('report/monthly_purchase/'.($year-1))}}"><i class="fa fa-arrow-left"></i> {{trans('file.Previous')}}</a></th>
                                <th colspan="10" class="text-center">{{$year}}</th>
                                <th><a href="{{url('report/monthly_purchase/'.($year+1))}}">{{trans('file.Next')}} <i class="fa fa-arrow-right"></i></a></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                              <td><strong>January</strong></td>
                              <td><strong>February</strong></td>
                              <td><strong>March</strong></td>
                              <td><strong>April</strong></td>
                              <td><strong>May</strong></td>
                              <td><strong>June</strong></td>
                              <td><strong>July</strong></td>
                              <td><strong>August</strong></td>
                              <td><strong>September</strong></td>
                              <td><strong>October</strong></td>
                              <td><strong>November</strong></td>
                              <td><strong>December</strong></td>
                            </tr>
                            <tr>
                                @foreach($total_amount as $key => $discount)
                                <td>
                                    @if($total_qty[$key] > 0)
                                    <strong>Total Jumlah Pembelian (Qty)</strong><br>
                                    <span>{{number_format($total_qty[$key], 0, '', '.')}}</span><br><br>
                                    @endif

                                    @if($total_amount[$key] > 0)
                                    <strong>Total Pembayaran</strong><br>
                                    <span>Rp. {{number_format($total_amount[$key], 0, '', '.')}}</span><br>
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<form id="report-form" method="get">
    <input type="hidden" name="warehouse_id_hidden" value="{{$warehouse_id}}">
</form>

@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#report").siblings('a').attr('aria-expanded','true');
    $("ul#report").addClass("show");
    $("ul#report #monthly-purchase-report-menu").addClass("active");

    $('#warehouse_id').val($('input[name="warehouse_id_hidden"]').val());
    $('.selectpicker').selectpicker('refresh');

    $('#warehouse_id').on("change", function(){
        let year = "{{$year}}";
        let warehouse_id = $(this).val();
        window.location.href = "{{url('/admin/report/monthly_purchase')}}/" + year + "?warehouse_id=" + warehouse_id;
    });
</script>
@endpush
