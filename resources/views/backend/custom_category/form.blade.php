@extends('backend.layout.main')
@section('content')
    <section class="form">
        <div class="container-fluid">
            @if (session()->has('not_permitted'))
                <div class="alert alert-danger alert-dismissible text-center">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    {{ session()->get('not_permitted') }}
                </div>
            @endif
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close"
                        data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h4>Kategori Custom</h4>
                            {{ Form::open(['route' => ['custom-category.destroy', $category->id], 'method' => 'DELETE']) }}
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Hapus Kategori Custom?')">Hapus Kategori Custom</button>
                            {{ Form::close() }}
                        </div>

                        <div class="card-body">
                            <form action="{{ route('custom-category.store', $category->id) }}" method="post">
                                @csrf

                                <div class="mb-3">
                                    <label for="category">Kategori</label>
                                    <input type="text" name="category" id="category" class="form-control" value="{{ $category->name }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="custom">Kategori Custom</label>
                                    <input type="text" name="custom" id="custom" class="form-control" value="{{ ($errors->has('custom')) ? @old('custom') : $category->custom }}">
                                </div>

                                <div class="mb-3">
                                    <a href="{{ route('custom-category.index') }}" class="btn btn-danger">Batal</a>
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
