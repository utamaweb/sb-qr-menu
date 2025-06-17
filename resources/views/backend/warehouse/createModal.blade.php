<div id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            {!! Form::open(['route' => 'outlet.store', 'method' => 'post']) !!}
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">Tambah Outlet</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close">
                    <span aria-hidden="true"><i class="dripicons-cross"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <p class="italic"><small>Inputan yang ditandai dengan <span class="text-danger">*</span> wajib diisi.</small></p>

                <div class="form-group">
                    <label>Nama Outlet <span class="text-danger">*</span></label>
                    <input type="text" name="name" required class="form-control">
                </div>

                <div class="form-group">
                    <label>Nama Bisnis <span class="text-danger">*</span></label>
                    @if(auth()->user()->hasRole('Superadmin'))
                        <select name="business_id" class="form-control">
                            <option value="">---Pilih Bisnis---</option>
                            @foreach($business as $bisnis)
                                <option value="{{ $bisnis->id }}">{{ $bisnis->name }}</option>
                            @endforeach
                        </select>
                    @elseif(auth()->user()->hasRole('Admin Bisnis'))
                        <input type="text" readonly class="form-control" name="business_name" value="{{ auth()->user()->business->name }}">
                        <input type="hidden" readonly name="business_id" value="{{ auth()->user()->business->id }}">
                    @endif
                </div>

                {{-- regional input --}}
                <div class="form-group">
                    <label>Regional <span class="text-danger">*</span></label>
                    <select name="regional_id" class="form-control">
                        <option value="">---Pilih Regional---</option>
                        @foreach($regionals as $regional)
                            <option value="{{ $regional->id }}">{{ $regional->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Alamat <span class="text-danger">*</span></label>
                    <input type="text" name="address" required class="form-control">
                </div>

                <div class="form-group">
                    <label>NPWPD</label>
                    <input type="text" name="npwpd" class="form-control">
                </div>

                <div class="form-group">
                    <label for="service">Jenis Service <span class="text-danger">*</span></label>
                    <select name="service" id="service" class="form-control">
                        <option value="1" {{ ($warehouse->is_self_service == 1) ? 'selected' : '' }}>Self Service</option>
                        <option value="0" {{ ($warehouse->is_self_service == 0) ? 'selected' : '' }}>Hanya Kasir</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="can_edit_order">Bisa Edit Order? <span class="text-danger">*</span></label>
                    <select name="can_edit_order" id="can_edit_order" class="form-control">
                        <option value="1" {{ ($warehouse->can_edit_order == 1) ? 'selected' : '' }}>Bisa Edit</option>
                        <option value="0" {{ ($warehouse->can_edit_order == 0) ? 'selected' : '' }}>Tidak Bisa</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tagihan">Tagihan <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" name="tagihan" id="tagihan" class="form-control" step="any"
                               value="{{ old('tagihan') }}" oninput="changeValue(this)" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="expired_at">Tanggal Expired <span class="text-danger">*</span></label>
                    <input type="date" name="expired_at" id="expired_at" class="form-control"
                           value="{{ old('expired_at') }}" required>
                </div>

                <div class="form-group">
                    <label for="whatsapp">Whatsapp <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">+62</span>
                        <input type="number" name="whatsapp" id="whatsapp" class="form-control"
                               value="{{ old('whatsapp') }}" required>
                        <button type="button" id="check-whatsapp" class="btn btn-primary">Check</button>
                    </div>
                </div>

                <input type="hidden" name="active_wa_number" id="active_wa_number" value="0">

                <div class="form-group mt-3">
                    <input type="submit" value="Submit" class="btn btn-primary">
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $('#check-whatsapp').click(function() {
        let phone = "62" + $('#whatsapp').val();

        // Check wa connection
        $.ajax({
            url: '{{ route("whatsapp.checkConnection") }}',
            method: 'GET',
            success: function(response) {
                if(response.data == true) {
                    // Check if phone number is wa registered
                    $.ajax({
                        url: '{{ route("whatsapp.checkNumber", ["number" => "__phone__"]) }}'.replace('__phone__', phone),
                        method: 'GET',
                        success: function(response) {
                            if (response.data == true) {
                                Swal.fire({
                                    text: 'Nomor telpon terdaftar di Whatsapp!',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                                $('#active_wa_number').val('1');
                            } else {
                                Swal.fire({
                                    text: 'Nomor telpon tidak terdaftar di Whatsapp!',
                                    icon: 'error',
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                            }
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                } else {
                    Swal.fire({
                        text: 'Tidak dapat terhubung ke Whatsapp!',
                        icon: 'error',
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }
            },
        });
    });
</script>
@endpush
