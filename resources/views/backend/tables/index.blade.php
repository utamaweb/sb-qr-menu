@extends('backend.layout.main-new')

@section('content')
    <x-content-card>
        <x-slot name="header">
            <span>Daftar Meja</span>

            <div>
                <a href="{{ route('tables.create') }}" class="btn btn-primary btn-sm">Tambah Meja</a>
            </div>
        </x-slot>

        <x-bt-table>
            <x-slot name="thead">
                <tr>
                    <th>No.</th>
                    <th>Nama Meja</th>
                    <th>Kode Meja</th>
                    <th>Aksi</th>
                </tr>
            </x-slot>

            @if (empty($tables))
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data</td>
                </tr>
            @else
                @foreach ($tables as $table)
                    <tr>
                        <td style="width: 5%">{{ $loop->iteration }}</td>
                        <td>{{ $table->name }}</td>
                        <td>{{ $table->code }}</td>
                        <td style="width: 15%">
                            <a href="{{ route('tables.edit', $table->id) }}" class="btn btn-sm btn-primary">Edit</a>
                            <form action="{{ route('tables.destroy', $table->id) }}" method="POST"
                                style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endif
        </x-bt-table>
    </x-content-card>
@endsection
