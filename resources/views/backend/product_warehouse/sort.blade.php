@extends('backend.layout.main')

@section('content')

    <section>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Urutkan Produk {{ $categoryParent->name }}</h4>

                    <div>
                        <a href="{{ route('produk-outlet.index') }}" class="btn btn-sm btn-primary"><i class="dripicons-arrow-left"></i> Kembali</a>
                    </div>
                </div>

                <div class="card-body">
                    <ul class="list-group" id="products">
                        @foreach ($products->sortBy('sort') as $item)
                            <li class="list-group-item" data-id="{{ $item->id }}">{{ $item->product->name }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="card-footer">
                    <form action="{{ route('produk-outlet.storeSort') }}" method="POST" id="sort-form">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="ids" id="ids">
                        <input type="hidden" name="category" value="{{ $categoryParent->id }}">

                        <button class="btn btn-sm btn-primary">Simpan</button>
                    </form>
                </div>

            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        let sortable = Sortable.create(products, {
            animation: 150,
            ghostClass: 'blue-background-class',
        });

        $('#sort-form').submit(function(e) {
            e.preventDefault();

            var ids = [];
            Array.from(sortable.el.children).forEach(function(item) {
                ids.push(item.getAttribute('data-id'));
            });

            $('#ids').val(ids.join(',')); // Menggabungkan ID menjadi string
            console.log(ids);
            this.submit(); // Mengirim form setelah mengisi ID
        });
    </script>
@endpush
