@extends('backend.layout.main')
@section('content')
<section>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h4 class="text-center mb-4">{{trans('file.Daily Purchase Report')}}</h4>

                <!-- Add warehouse filter only for Admin Bisnis or Report roles -->
                @if(auth()->user()->hasRole(['Admin Bisnis', 'Report']))
                <div class="row mb-4">
                    <div class="col-md-6 offset-md-3 text-center">
                        <form action="{{ url("admin/report/daily_purchase/$year/$month") }}" method="GET">
                            <div class="form-group mb-3">
                                <select name="warehouse_id" id="warehouse_id" class="form-control selectpicker" data-live-search="true">
                                    <option value="">Pilih Outlet</option>
                                    @foreach($lims_warehouse_list as $warehouse_item)
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
                    <h5 class="text-center mb-3">Outlet: Semua</h5>
                @elseif(!auth()->user()->hasRole(['Admin Bisnis', 'Report']))
                    <h5 class="text-center mb-3">Outlet: {{ auth()->user()->warehouse->name ?? 'Tidak Ditemukan' }}</h5>
                @endif

                <div class="table-responsive mt-4">
                    <table class="table table-bordered" style="border-top: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                        <thead>
                            <tr>
                                <th><a href="{{url('admin/report/daily_purchase/'.$prev_year.'/'.$prev_month)}}{{ $warehouse_id ? '?warehouse_id='.$warehouse_id : '' }}"><i class="fa fa-arrow-left"></i> {{trans('file.Previous')}}</a></th>
                            	<th colspan="5" class="text-center">{{date("F", strtotime($year.'-'.$month.'-01')).' ' .$year}}</th>
                            	<th><a href="{{url('admin/report/daily_purchase/'.$next_year.'/'.$next_month)}}{{ $warehouse_id ? '?warehouse_id='.$warehouse_id : '' }}">{{trans('file.Next')}} <i class="fa fa-arrow-right"></i></a></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Sunday</strong></td>
                                <td><strong>Monday</strong></td>
                                <td><strong>Tuesday</strong></td>
                                <td><strong>Wednesday</strong></td>
                                <td><strong>Thrusday</strong></td>
                                <td><strong>Friday</strong></td>
                                <td><strong>Saturday</strong></td>
                            </tr>
                            <?php
                            	$i = 1;
                            	$flag = 0;
                            	while ($i <= $number_of_day) {
                            		echo '<tr>';
                            		for($j=1 ; $j<=7 ; $j++){
                            			if($i > $number_of_day)
                            				break;

                            			if($flag){
                            				if($year.'-'.$month.'-'.$i == date('Y').'-'.date('m').'-'.(int)date('d'))
                            					echo '<td><p style="color:red"><strong>'.$i.'</strong></p>';
                            				else
                            					echo '<td><p><strong>'.$i.'</strong></p>';

                            				if(isset($total_qty[$i]) && $total_qty[$i]){
                            					echo '<strong>'."Jumlah Produk".'</strong><br><span>'.number_format($total_qty[$i], 0, '', '.').'</span><br><br>';
                            				}
                            				if(isset($total_amount[$i]) && $total_amount[$i]){
                            					echo '<strong>'."Total Pengeluaran".'</strong><br><span>Rp. '.number_format($total_amount[$i], 0, '', '.').'</span><br><br>';
                            				}
                            				echo '</td>';
                            				$i++;
                            			}
                            			elseif($j == $start_day){
                            				if($year.'-'.$month.'-'.$i == date('Y').'-'.date('m').'-'.(int)date('d'))
                            					echo '<td><p style="color:red"><strong>'.$i.'</strong></p>';
                            				else
                            					echo '<td><p><strong>'.$i.'</strong></p>';

                                            if(isset($total_qty[$i]) && $total_qty[$i]){
                            					echo '<strong>'."Jumlah Produk".'</strong><br><span>'.number_format($total_qty[$i], 0, '', '.').'</span><br><br>';
                            				}
                            				if(isset($total_amount[$i]) && $total_amount[$i]){
                            					echo '<strong>'."Total Pengeluaran".'</strong><br><span>Rp. '.number_format($total_amount[$i], 0, '', '.').'</span><br><br>';
                            				}
                            				echo '</td>';
                            				$flag = 1;
                            				$i++;
                            				continue;
                            			}
                            			else {
                            				echo '<td></td>';
                            			}
                            		}
                            	    echo '</tr>';
                            	}
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#report").siblings('a').attr('aria-expanded','true');
    $("ul#report").addClass("show");
    $("ul#report #daily-purchase-report-menu").addClass("active");

    // Initialize selectpicker if you're using Bootstrap Select
    $('.selectpicker').selectpicker({
        style: 'btn-link',
    });
</script>
@endpush
