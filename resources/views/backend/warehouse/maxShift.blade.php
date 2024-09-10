@extends('backend.layout.main')
@section('content')
    <section class="form">
        <div class="container-fluid">
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close"
                        data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">Konfigurasi Jumlah Shift Maksimal</div>

                        <div class="card-body">
                            <form action="{{ route('maxShiftUpdate') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="max_shift">Jumlah Shift Maksimal</label>
                                    <input type="number" name="max_shift" id="max_shift" class="form-control" autocomplete="off" required placeholder="Jumlah Shift Maksimal" value="{{ auth()->user()->warehouse->max_shift_count }}">
                                </div>

                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
