<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Outlet</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="italic"><small>Input yang ditandai dengan * wajib diisi.</small></p>
                <form method="post" id="editForm">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" id="editId">

                    {{-- Name input --}}
                    <div class="form-group">
                        <label>Nama Outlet *</label>
                        <input type="text" name="name" id="editName" required class="form-control">
                    </div>
                    {{-- End of name input --}}

                    {{-- Business input --}}
                    <div class="form-group">
                        <label>Nama Bisnis *</label>
                        @if(auth()->user()->hasRole('Superadmin'))
                        <select name="business_id" class="form-control" id="editBusiness">
                            <option value="">---Pilih Bisnis---</option>
                            @foreach($business as $bisnis)
                                <option value="{{$bisnis->id}}">{{$bisnis->name}}</option>
                            @endforeach
                        </select>
                        @elseif(auth()->user()->hasRole('Admin Bisnis'))
                            <input type="text" readonly class="form-control" name="business_name" value="{{auth()->user()->business->name}}">
                            <input type="hidden" readonly class="form-control" name="business_id" value="{{auth()->user()->business->id}}">
                        @endif
                    </div>
                    {{-- End of business input --}}

                    {{-- Address input --}}
                    <div class="form-group">
                        <label>Alamat *</label>
                        <input type="text" name="address" id="editAddress" required class="form-control">
                    </div>
                    {{-- End of address input --}}

                    {{-- Service input --}}
                    <div class="form-group">
                        <label for="editService">Jenis Service *</label>
                        <select name="service" id="editService" class="form-control">
                            <option value="1">Self Service</option>
                            <option value="0">Hanya Kasir</option>
                        </select>
                    </div>
                    {{-- End of service input --}}

                    {{-- Tagihan input --}}
                    <div class="form-group">
                        <label for="editTagihan">Tagihan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" name="tagihan" id="editTagihan" class="form-control" step="any" value="{{old('tagihan')}}" oninput="changeValue(this)" required>
                        </div>
                    </div>
                    {{-- End of tagihan input --}}

                    {{-- Expired date input --}}
                    <div class="form-group">
                        <label for="editExpiredAt">Tanggal Expired <span class="text-danger">*</span></label>
                        <input type="date" name="expired_at" id="editExpiredAt" class="form-control" value="{{old('expired_at')}}" required>
                    </div>
                    {{-- End of expired date input --}}

                    {{-- Submit button --}}
                    <input type="submit" value="Submit" class="btn btn-primary">
                    {{-- End of submit button --}}
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function editModal(id) {
        $('#editId').val(id);

        // Get outlet data
        $.ajax({
            url: "{{ route('outlet.getById', ['id' => '__id__']) }}".replace('__id__', id),
            type: 'GET',
            dataType: 'json',
            success: function (data) {

                // Update form action value
                $('#editForm').attr('action', "{{ route('outlet.update', ['outlet' => '__id__']) }}".replace('__id__', id));

                $('#editName').val(data.name);
                $('#editAddress').val(data.address);
                $('#editService').selectpicker('val', data.is_self_service);
                $('#editBusiness').selectpicker('val', data.business_id);
                $('#editTagihan').val(formatNumber(data.tagihan));
                $('#editExpiredAt').val(data.expired_at);

                $('#editModal').modal('show');
            },
            error: function (error) {
                console.error('Error fetching outlet data:', error);
                alert('Terjadi kesalahan saat mengambil data outlet.');
            }
        });
    }
</script>
@endpush
