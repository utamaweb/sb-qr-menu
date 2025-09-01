@extends('backend.layout.main-new')
@section('content')
    <x-content-card>
        <x-slot:header>
            <span>Tambah Meja</span>
            <div>
                <a href="{{ route('tables.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
            </div>
        </x-slot:header>

        <form action="{{ route('tables.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Nama Meja</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                    value="{{ old('name') }}" autocomplete="off" required>
                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </x-content-card>
@endsection
