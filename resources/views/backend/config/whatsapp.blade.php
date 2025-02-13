@extends('backend.layout.main')

@section('content')
<section>
    <div class="container-fluid">
        @include('includes.alerts')

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span>Whatsapp Configuration</span>
            </div>

            <div class="card-body">
                <form action="{{ route('whatsapp.store') }}" method="POST">
                    @csrf

                    {{-- API URL input --}}
                    <div class="mb-3">
                        <label for="api_url" class="form-label">API URL</label>
                        <input type="text" class="form-control" id="api_url" name="config[api_url]" value="{{ old('api_url', ($configs->where('key', 'api_url')->first()->value ?? '')) }}" placeholder="https://customer-api.whatsapp.com">
                    </div>
                    {{-- End of API URL input --}}

                    {{-- Session name input --}}
                    <div class="mb-3">
                        <label for="session_name" class="form-label">Session Name</label>
                        <input type="text" class="form-control" id="session_name" name="config[session_name]" value="{{ old('session_name', ($configs->where('key', 'session_name')->first()->value ?? '')) }}" placeholder="session_name">
                    </div>
                    {{-- End of session name input --}}

                    {{-- Submit button --}}
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                    {{-- End of submit button --}}
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
