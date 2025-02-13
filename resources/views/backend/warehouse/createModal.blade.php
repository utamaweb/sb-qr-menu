<div id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            {!! Form::open(['route' => 'outlet.store', 'method' => 'post']) !!}
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">Tambah Outlet</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
                <p class="italic"><small>Inputan yang ditandai dengan <span class="text-danger">*</span> wajib diisi.</small></p>
                <form>
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
                            <option value="{{$bisnis->id}}">{{$bisnis->name}}</option>
                            @endforeach
                        </select>
                        @elseif(auth()->user()->hasRole('Admin Bisnis'))
                        <input type="text" readonly class="form-control" name="business_name" value="{{auth()->user()->business->name}}">
                        <input type="hidden" readonly class="form-control" name="business_id" value="{{auth()->user()->business->id}}">
                        @endif
                    </div>
                    <div class="form-group">
                        <label>Alamat <span class="text-danger">*</span></label>
                        <input type="text" name="address" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="service">Jenis Service <span class="text-danger">*</span></label>
                        <select name="service" id="service" class="form-control">
                            <option value="1" {{ ($warehouse->is_self_service == 1) ? 'selected' : '' }}>Self Service</option>
                            <option value="0" {{ ($warehouse->is_self_service == 0) ? 'selected' : '' }}>Hanya Kasir</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tagihan">Tagihan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" name="tagihan" id="tagihan" class="form-control" step="any" value="{{old('tagihan')}}" oninput="changeValue(this)" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="expired_at">Tanggal Expired <span class="text-danger">*</span></label>
                        <input type="date" name="expired_at" id="expired_at" class="form-control" value="{{old('expired_at')}}" required>
                    </div>

                    {{-- Whatsapp input --}}
                    <div class="form-group">
                        <label for="whatsapp">Whatsapp <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">+62</span>
                            <input type="number" name="whatsapp" id="whatsapp" class="form-control" value="{{old('whatsapp')}}" required>
                        </div>
                    </div>
                    {{-- End of whatsapp input --}}

                    <input type="submit" value="Submit" class="btn btn-primary">
            </form>
        </div>
        {{ Form::close() }}
        </div>
    </div>
</div>
