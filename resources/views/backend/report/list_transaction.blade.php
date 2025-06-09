@extends('backend.layout.main')

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">List Transaksi Aktual</h3>
                        <h4 class="text-center mt-3">Tanggal: {{ \Carbon\Carbon::parse($start_date)->translatedFormat('j M Y') }} s/d {{ \Carbon\Carbon::parse($end_date)->translatedFormat('j M Y') }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="" method="GET">
                            <div class="form-group">
                                <label for=""><strong>Pilih Tanggal</strong></label>
                                <div class="input-group">
                                    <input type="text" name="start_date" id="start_date" class="form-control date" required value="{{ $start_date }}">
                                    <span class="input-group-text">s/d</span>
                                    <input type="text" name="end_date" id="end_date" class="form-control date" required value="{{ $end_date }}">
                                </div>
                                <div id="dateError" class="text-danger mt-2" style="display: none;">
                                    Rentang tanggal tidak boleh lebih dari 30 hari!
                                </div>
                            </div>

                            @if(auth()->user()->hasRole(['Admin Bisnis', 'Report']))
                                <div class="form-group">
                                    <label><strong>Pilih Regional</strong></label>
                                    <select id="regional-select" name="regional_id" class="form-control selectpicker" data-live-search="true" data-live-search-style="begins"
                                    title="Pilih regional">
                                        <option value="all" {{ $regional_request == 'all' ? 'selected' : ''}}>Semua Regional</option>
                                        @foreach($regionals as $regional)
                                        <option value="{{$regional->id}}" {{$regional->id == $regional_request ? 'selected' : ''}}>{{$regional->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label><strong>Pilih Outlet</strong></label>
                                    <select id="warehouse-select" name="warehouse_id" class="form-control selectpicker" data-live-search="true" data-live-search-style="begins"
                                    title="Pilih outlet">
                                        <option value="all" {{ $warehouse_request == 'all' ? 'selected' : ''}}>Semua Outlet</option>
                                        @foreach($warehouses as $warehouse)
                                        <option value="{{$warehouse->id}}" {{$warehouse->id == $warehouse_request ? 'selected' : ''}}>{{$warehouse->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- Transaction List Card -->
        <div class="card">
            <div class="card-header">
                <span>List Transaksi</span>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="ingredient-table" class="table table-hover" style="width: 100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tanggal | Jam</th>
                                <th>Outlet</th>
                                <th>Antrian</th>
                                <th>Tipe Pesanan</th>
                                <th>Tipe Pembayaran</th>
                                <th>Total Tagihan</th>
                                <th>Jumlah Pesanan</th>
                                <th>Status</th>
                                <th class="not-exported">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $key => $transaction)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $transaction->date }} | {{ $transaction->created_at->format('H:i:s') }}</td>
                                <td>{{ $transaction->warehouse->name }}</td>
                                <td>{{ $transaction->sequence_number }}</td>
                                <td>{{ $transaction->order_type->name }}</td>
                                <td>{{ $transaction->payment_method }} ({{ $transaction->category_order }})</td>
                                <td>Rp. {{ number_format($transaction->total_amount, 0, '', '.') }}</td>
                                <td>{{ number_format($transaction->total_qty, 0, '', '.') }}</td>
                                <td>
                                    @if($transaction->status == "Lunas")
                                        <div class="badge badge-success">Lunas</div>
                                    @elseif($transaction->status == "Pending")
                                        <div class="badge badge-warning">Pending</div>
                                    @else
                                        <div class="badge badge-danger">Batal</div>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-success"
                                        onclick="getDetails({{ $transaction->id }})">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">Tidak ada data transaksi</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Modal for transaction detail --}}
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detail Transaksi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table" id="transaction-detail">
                        <thead>
                            <th>#</th>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                    <table class="table">
                        <tr class="text-center">
                            <td>
                                <h5>Total Qty</h5>
                            </td>
                            <td>
                                <h5 id="total-qty"></h5>
                            </td>
                        </tr>
                        <tr class="text-center">
                            <td>
                                <h5>Total</h5>
                            </td>
                            <td>
                                <h5 id="total"></h5>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    {{-- End of modal for transaction detail --}}
@endsection

@push('scripts')
<script type="text/javascript">
    // Set active menu
    $("#list-transaction").addClass("active");

    // Initialize DataTable
    $('#ingredient-table').DataTable({
        "order": [],
        'language': {
            'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
            "info": '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            "search": 'Cari',
            'paginate': {
                'previous': '<i class="dripicons-chevron-left"></i>',
                'next': '<i class="dripicons-chevron-right"></i>'
            }
        },
        'columnDefs': [],
        'select': {
            style: 'multi',
            selector: 'td:first-child'
        },
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row"lfB>rtip',
        buttons: [
            {
                extend: 'pdf',
                text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
            },
            {
                extend: 'excel',
                text: '<i title="export to excel" class="dripicons-document-new"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
            },
            {
                extend: 'csv',
                text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
            },
            {
                extend: 'print',
                text: '<i title="print" class="fa fa-print"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
            },
            {
                extend: 'colvis',
                text: '<i title="column visibility" class="fa fa-eye"></i>',
                columns: ':gt(0)'
            },
        ],
    });
</script>

<script>
    $(document).ready(function() {
        // Fungsi untuk memeriksa tanggal
        function validateDates() {
            var startDate = new Date($("input[name='start_date']").val());
            var endDate = new Date($("input[name='end_date']").val());

            // Jika tanggal mulai lebih besar dari tanggal selesai
            if (startDate > endDate) {
                alert("Tanggal Mulai tidak boleh lebih besar dari Tanggal Selesai.");
                $("input[name='start_date']").val(''); // Mengosongkan input tanggal mulai
                $("input[name='end_date']").val(''); // Mengosongkan input tanggal selesai
            }
        }

        // Event listener untuk perubahan pada input tanggal
        $("input[name='start_date'], input[name='end_date']").on("change", function() {
            validateDates();
        });
    });
    </script>
   <script>
    $(function() {
        // Inisialisasi datepicker
        $("#start_date, #end_date").datepicker({
            dateFormat: 'yy-mm-dd', // Format sesuai dengan backend
            onSelect: function(selectedDate) {
                var startDate = $("#start_date").datepicker("getDate");
                var endDate = $("#end_date").datepicker("getDate");

                // Jika start_date dipilih, set maxDate di end_date
                if (this.id == "start_date") {
                    $("#end_date").datepicker("option", "minDate", startDate);
                    if (startDate) {
                        var maxEndDate = new Date(startDate);
                        maxEndDate.setDate(maxEndDate.getDate() + 30);
                        $("#end_date").datepicker("option", "maxDate", maxEndDate);
                    }
                }

                // Reset maxDate jika end_date diubah
                if (this.id == "end_date" && !$("#start_date").val()) {
                    $("#end_date").datepicker("option", "maxDate", null);
                }
            }
        });

        // Validasi form sebelum submit
        $("form").on("submit", function(e) {
            var startDate = $("#start_date").datepicker("getDate");
            var endDate = $("#end_date").datepicker("getDate");
            var errorDiv = $("#dateError");

            errorDiv.hide();

            if (!startDate || !endDate) {
                alert("Silakan pilih tanggal mulai dan akhir.");
                e.preventDefault();
                return false;
            }

            var diffTime = Math.abs(endDate - startDate);
            var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays > 30) {
                errorDiv.show();
                e.preventDefault();
                return false;
            }

            return true;
        });
    });

    // Add validation to check if end date is before start date
    $("#end_date").on("change", function() {
        var startDate = new Date($("#start_date").val());
        var endDate = new Date($(this).val());

        if (endDate < startDate) {
            alert("Tanggal akhir tidak boleh sebelum tanggal mulai.");
            $(this).val('');
            return false;
        }
    });

    // Also validate when form is submitted
    $("form").on("submit", function(e) {
        var startDate = new Date($("#start_date").val());
        var endDate = new Date($("#end_date").val());

        if (endDate < startDate) {
            alert("Tanggal akhir tidak boleh sebelum tanggal mulai.");
            $("#end_date").val('');
            e.preventDefault();
            return false;
        }
    });
    </script>


<script>
    // Function to format number into number format
        function formatNumber(number) {
            // Remove non-digit characters
            var numericValue = number.toString().replace(/\D/g, "");

            // Add thousand separators
            var formattedNumber = numericValue.replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            return formattedNumber;
        }

    // Script for transaction detail Modal
    function getDetails(id) {
        // Show modal first with loading indicator
        $('#transaction-detail tbody').html('<tr><td colspan="5" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading data...</td></tr>');
        $('#total-qty').html('<i class="fa fa-spinner fa-spin"></i>');
        $('#total').html('<i class="fa fa-spinner fa-spin"></i>');
        $('#detailModal').modal('show');

        // Then fetch the data
        $.ajax({
            type: "GET",
            url: `/admin/close-cashier/transaction/${id}`,
            success: function(data) {
                // Delete table body items
                $('#transaction-detail tbody').empty();
                let total = 0;
                let totalQty = 0;
                // Loop for each data
                $.each(data, function(key, value) {
                    // Append table body items
                    $('#transaction-detail tbody').append(`
                        <tr>
                            <td>${key + 1}</td>
                            <td>${value.product_name}</td>
                            <td>Rp. ${formatNumber(value.product_price)}</td>
                            <td>${value.qty}</td>
                            <td>Rp. ${formatNumber(value.subtotal)}</td>
                        </tr>
                    `);

                    // Count total
                    total += parseInt(value.subtotal);
                    totalQty += parseInt(value.qty);
                });

                $('#total-qty').html(totalQty);
                $('#total').html(`Rp. ${formatNumber(total)}`);
            },
            error: function(xhr, status, error) {
                $('#transaction-detail tbody').html('<tr><td colspan="5" class="text-center text-danger">Error: Failed to load data</td></tr>');
                $('#total-qty').html('-');
                $('#total').html('-');
                console.error('Error dalam pengambilan data:', status, error);
            }
        });
    }
    // End of Script for transaction detail Modal
</script>


<script>

// Add CSS for the loading indicator
$('head').append(`
    <style>
        .loader-container {
            display: none;
            position: relative;
            width: 100%;
            height: 30px;
            text-align: center;
            margin-top: 5px;
        }
        .loader {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(0,0,0,0.1);
            border-radius: 50%;
            border-top-color: #3498db;
            animation: spin 1s ease-in-out infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
`);

// Add loading indicator element after warehouse select
$('#warehouse-select').after('<div class="loader-container"><div class="loader"></div><small class="ml-2">Loading outlets...</small></div>');

// Handle Regional-Warehouse dependency
$(document).ready(function() {
    // On regional select change
    $('#regional-select').change(function() {
        var regionalId = $(this).val();

        // Show loading indicator
        $('.loader-container').show();

        // Disable warehouse select while loading
        $('#warehouse-select').prop('disabled', true).selectpicker('refresh');

        // Make AJAX request
        $.ajax({
            url: '{{ route("getWarehousesByRegional", "") }}/' + regionalId,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Clear current options
                $('#warehouse-select').empty();

                // Add "All Outlets" option
                $('#warehouse-select').append('<option value="all">Semua Outlet</option>');

                // Add warehouses from response
                $.each(data, function(index, warehouse) {
                    $('#warehouse-select').append('<option value="' + warehouse.id + '">' + warehouse.name + '</option>');
                });

                // Enable warehouse select and refresh
                $('#warehouse-select').prop('disabled', false).selectpicker('refresh');

                // Hide loading indicator
                $('.loader-container').hide();
            },
            error: function(xhr, status, error) {
                console.error("Error fetching warehouses: " + error);

                // Hide loading indicator even on error
                $('.loader-container').hide();

                // Re-enable warehouse select
                $('#warehouse-select').prop('disabled', false).selectpicker('refresh');

                // Show error message
                alert("Error loading outlets. Please try again.");
            }
        });
    });
});
</script>
@endpush
