@extends('backend.layout.main')

@section('content')

<section>
    <div class="container-fluid">
        @if ($errors->has('name'))
                <div class="alert alert-danger alert-dismissible text-center">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>{{ $errors->first('name') }}
                </div>
            @endif
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close"
                        data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
            @endif
            @if (session()->has('not_permitted'))
                <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close"
                        data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
            @endif
    </div>

    <div class="table-responsive">
        <table id="category-table" class="table">
            <thead>
                <th class="text-center">#</th>
                <th>Nama Kategori</th>
                <th>Nama Kategori Custom</th>
                <th class="not-exported">Aksi</th>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->custom }}</td>
                        <td>
                            <a href="{{ route('custom-category.form', $category->id) }}" class="btn btn-success">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

@endsection
