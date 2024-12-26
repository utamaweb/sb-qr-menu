@extends('backend.layout.main')

@section('content')

    <section>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Urutkan Produk {{ $categoryParent->name }}</h4>

                    <div>
                        <a href="{{  route('produk-outlet.index') }}" class="btn btn-sm btn-primary"><i class="dripicons-arrow-left"></i> Kembali</a>
                    </div>
                </div>

                <div class="card-body">
                    <ul class="list-group" id="products">
                        @foreach ($products as $item)
                            <li class="list-group-item">{{ $item->product->name }}</li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        Sortable.create(products, {
            animation: 150,
            ghostClass: 'blue-background-class',
        });
    </script>
@endpush
