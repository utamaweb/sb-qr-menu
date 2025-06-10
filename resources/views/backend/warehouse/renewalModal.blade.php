<div class="modal fade" id="renewalModal" tabindex="-1" role="dialog" aria-labelledby="renewalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renewalModalLabel">Pembayaran Tagihan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" id="renewalForm">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" id="renewalId">

                    {{-- Name input --}}
                    <div class="form-group">
                        <label>Nama Outlet</label>
                        <input type="text" name="name" id="renewalName" required class="form-control" disabled>
                    </div>
                    {{-- End of name input --}}

                    {{-- Business input --}}
                    <div class="form-group">
                        <label>Nama Bisnis</label>
                        <input type="text" name="" id="renewalBusiness" class="form-control" disabled>
                    </div>
                    {{-- End of business input --}}

                    {{-- Address input --}}
                    <div class="form-group">
                        <label>Alamat</label>
                        <input type="text" name="address" id="renewalAddress" required class="form-control" disabled>
                    </div>
                    {{-- End of address input --}}

                    {{-- Service input --}}
                    <div class="form-group">
                        <label for="renewalService">Jenis Service</label>
                        <input type="text" name="service" id="renewalService" class="form-control" disabled>
                    </div>
                    {{-- End of service input --}}

                    {{-- Service input --}}
                    <div class="form-group">
                        <label for="renewalEditOrder">Bisa Edit Order?</label>
                        <input type="text" name="can_edit_order" id="renewalEditOrder" class="form-control" disabled>
                    </div>
                    {{-- End of service input --}}

                    {{-- Tagihan input --}}
                    <div class="form-group">
                        <label for="renewalTagihan">Tagihan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" name="tagihan" id="renewalTagihan" class="form-control" step="any" required disabled>
                        </div>
                    </div>
                    {{-- End of tagihan input --}}

                    {{-- Expired date input --}}
                    <div class="form-group">
                        <label for="renewalExpiredAt">Tanggal Expired <span class="text-danger">*</span></label>
                        <input type="date" name="expired_at" id="renewalExpiredAt" class="form-control" value="{{old('expired_at')}}" required>
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
    function renewalModal(id) {
        $('#renewalId').val(id);

        // Get outlet data
        $.ajax({
            url: "{{ route('outlet.getById', ['id' => '__id__']) }}".replace('__id__', id),
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                console.log(data.tagihan);

                // Update form action value
                $('#renewalForm').attr('action', "{{ route('outlet.renewal', ['id' => '__id__']) }}".replace('__id__', id));

                $('#renewalName').val(data.name);
                $('#renewalAddress').val(data.address);
                $('#renewalService').val(data.is_self_service ? 'Self Service' : 'Hanya Kasir');
                $('#renewalEditOrder').val(data.can_edit_order ? 'Bisa Edit' : 'Tidak Bisa');
                $('#renewalBusiness').val(data.business.name);
                $('#renewalTagihan').val(formatNumber(data.tagihan == null ? 0 : data.tagihan));
                $('#renewalExpiredAt').val(data.expired_at);
                $('#renewalExpiredAt').attr('min', data.expired_at);

                $('#renewalModal').modal('show');
            },
            error: function (error) {
                console.error('Error fetching outlet data:', error);
                alert('Terjadi kesalahan saat mengambil data outlet.');
            }
        });
    }
</script>
@endpush
