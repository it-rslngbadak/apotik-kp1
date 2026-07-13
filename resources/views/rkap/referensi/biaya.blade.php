@extends('layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">Referensi Biaya</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('pendapatan/list') }}">Referensi Biaya</a>
                                </li>
                                <li class="breadcrumb-item active">All Biaya Referensi Units</li>
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
                            <div class="page-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="page-title">List Referensi Biaya Unit</h3>
                                    </div>
                                    <div class="col-auto text-end float-end ms-auto download-grp">
                                        <a href="{{ route('biaya/list') }}" class="btn btn-outline-gray me-2 active">
                                            <i class="fa fa-list" aria-hidden="true"></i>
                                        </a>
                                        {{-- <a href="{{ route('student/grid') }}" class="btn btn-outline-gray me-2">
                                            <i class="fa fa-th" aria-hidden="true"></i>
                                        </a> --}}
                                        {{-- <a href="#" class="btn btn-outline-primary me-2"><i
                                                class="fas fa-download"></i> Download</a> --}}
                                    </div>
                                </div>
                            </div>

                            {{-- Form Filter --}}
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="filterUnit" class="form-label">Unit</label>
                                    <select id="filterUnit" class="form-control select2" style="width: 100%;">
                                        <option value="">-- Pilih Unit --</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->nama_unit }}">{{ $unit->nama_unit }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" id="btnFilter" class="btn btn-primary w-100">
                                        <i class="fa fa-search"></i> Filter
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-stripped table table-hover table-center mb-0" id="DataList">
                                    <thead class="student-thread">
                                        <tr>
                                            <th>Nama Transaksi</th>
                                            <th>Jumlah</th>
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
    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2();

            let table = $('#DataList').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                searching: true,
                order: [],
                ajax: {
                    url: "{{ route('get-biayas-data') }}",
                    cache: false,
                    data: function(d) {
                        d.nama_unit = $('#filterUnit').val();
                    }
                },
                columns: [{
                        data: 'nama_transaksi',
                        name: 'nama_transaksi'
                    },
                    {
                        data: 'jumlah',
                        name: 'jumlah'
                    }
                ]
            });

            // Reload data saat tombol filter diklik
            $('#btnFilter').on('click', function() {
                table.ajax.reload();
            });
        });
    </script>
@endsection

@endsection
