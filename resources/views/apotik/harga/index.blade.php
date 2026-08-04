@extends('layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">Harga Obat</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('list-harga') }}">List Harga</a></li>
                                <li class="breadcrumb-item active">All Item</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            {{-- message --}}
            {!! Toastr::message() !!}
            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table comman-shadow">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-stripped table table-hover table-center mb-0" id="HargaList">
                                    <thead class="student-thread">
                                        <tr>
                                            <th>Nama Item</th>
                                            <th>Farmalkes ID</th>
                                            <th>Satuan</th>
                                            <th>Harga</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@section('script')
    {{-- get user all js --}}
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#HargaList').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                searching: true,
                ajax: {
                    url: "{{ route('get-data-harga') }}",
                },
                columns: [{
                        data: 'farmalkes_id',
                        name: 'farmalkes_id'
                    },
                    {
                        data: 'farmalkes_desc',
                        name: 'farmalkes_desc'
                    },
                    {
                        data: 'satuan',
                        name: 'satuan',
                    },
                    {
                        data: 'harga_jual',
                        name: 'harga_jual',
                        ordering: false,
                        searching: false,
                    },
                ],
                // matikan binding search bawaan biar gak double-trigger
                initComplete: function() {
                    var api = this.api();
                    var minChars = 3;
                    var delay = 500; // ms
                    var debounceTimer;

                    $('div.dataTables_filter input')
                        .off() // lepas semua event bawaan DataTables (keyup/input)
                        .on('keyup', function() {
                            var searchValue = this.value.trim();

                            clearTimeout(debounceTimer);

                            // hanya search kalau >= 3 karakter, ATAU kosong (buat reset)
                            if (searchValue.length >= minChars || searchValue.length === 0) {
                                debounceTimer = setTimeout(function() {
                                    api.search(searchValue).draw();
                                }, delay);
                            }
                        });
                }
            });
        });
    </script>
@endsection

@endsection
