@extends('backend.layout.main')

@section('content')
<section>
    <div class="container-fluid">
        @include('includes.alerts')

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span>Whatsapp Configuration</span>
                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#whatsappModal">Connection</button>
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

{{-- Whatsapp Connection modal --}}
<div class="modal fade" id="whatsappModal" tabindex="-1" aria-labelledby="whatsappModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="whatsappModalLabel">Koneksi WhatsApp</h1>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                {{-- Not connected alert --}}
                <div class="alert alert-danger" role="alert" style="display: none" id="notConnected">Tidak terhubung ke layanan Whatsapp!</div>
                {{-- End of not connected alert --}}

                {{-- Account not connected alert --}}
                <div class="alert alert-danger" role="alert" style="display: none" id="notAccount">Tidak ada akun Whatsapp yang terhubung!</div>
                {{-- End of account not connected alert --}}

                {{-- Refresh alert --}}
                <div class="alert alert-primary" role="alert" style="display: none" id="refresh">Tekan tombol refresh setelah berhasil scan QRCode!</div>
                {{-- End of refresh alert --}}

                {{-- Whatsapp information --}}
                <div class="alert alert-primary" role="alert" style="display: none" id="waInfo"></div>
                {{-- End of whatsapp information --}}

                {{-- QRCode --}}
                <div class="d-flex justify-content-center">
                    <canvas id="qrcode" style="display: none"></canvas>
                </div>
                {{-- End of QRCode --}}

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="refreshProcess()">Refresh</button>
                <button type="button" class="btn btn-danger" onclick="logoutProcess()" style="display: none" id="logoutButton">Logout</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
{{-- End of whatsapp connection modal --}}
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>

<script>
    $(document).ready(async function() {
        let isConnected = await checkConnection();
        if (!isConnected) {
            $('#notConnected').attr('style', 'display: block');
        } else {
            let details = await getDetail();
            if(details.error) {
                $('#notAccount').attr('style', 'display: block');

                let session = await createSession();

                if (session.error == false) {
                    const qr = new QRious({
                        element: document.getElementById('qrcode'),
                        value: session.data.qr,
                        size: 300
                    });

                    $('#refresh').attr('style', 'display: block');
                    $('#notConnected').attr('style', 'display: none');

                    $('#qrcode').attr('style', 'display: block');
                }

            } else {
                if(details.data.length == 0) {
                    // Logout session
                    let logout = await logoutSession();

                    if(logout) {
                        // Reload the page
                        location.reload();
                    }
                } else {
                    let accountDetails = `Whatsapp terhubung ke akun : ${details.data.data.name} - (${details.data.data.id})`;

                    $('#waInfo').html(accountDetails);
                    $('#waInfo').attr('style', 'display: block');
                    $('#logoutButton').attr('style', 'display: block');
                }
            }
        }
    });

    function checkConnection() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '{{ route('whatsapp.checkConnection') }}',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.error == false) {
                        resolve(data.data); // Mengembalikan data jika tidak ada error
                    } else {
                        resolve(false); // Mengembalikan false jika ada error
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    reject(new Error("AJAX request failed: " + textStatus)); // Menangani error AJAX
                }
            });
        });
    }

    function getDetail() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '{{ route('whatsapp.sessionDetails') }}',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.error == false) {
                        resolve(data); // Mengembalikan data jika tidak ada error
                    } else {
                        resolve(data); // Mengembalikan false jika ada error
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    reject(new Error("AJAX request failed: " + textStatus)); // Menangani error AJAX
                }
            });
        });
    }

    function createSession() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '{{ route('whatsapp.createSession') }}',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.error == false) {
                        resolve(data); // Mengembalikan data jika tidak ada error
                    } else {
                        resolve(false); // Mengembalikan false jika ada error
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    reject(new Error("AJAX request failed: " + textStatus)); // Menangani error AJAX
                }
            });
        });
    }

    function logoutSession() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '{{ route('whatsapp.logout') }}',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.error == false) {
                        resolve(true); // Mengembalikan data jika tidak ada error
                    } else {
                        resolve(false); // Mengembalikan false jika ada error
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    reject(new Error("AJAX request failed: " + textStatus)); // Menangani error AJAX
                }
            });
        });
    }

    async function logoutProcess() {
        // Logout session
        let logout = await logoutSession();

        if(logout) {
            // Reload the page
            location.reload();
        }
    }

    function refreshProcess() {
        location.reload();
    }
</script>
@endpush
