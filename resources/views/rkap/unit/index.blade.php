@extends('layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">UNIT</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('unit/list') }}">Unit</a></li>
                                <li class="breadcrumb-item active">All Units</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            {{-- message --}}
            {!! Toastr::message() !!}
            {{-- <div class="student-group-form mb-3">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label>Dari Tanggal</label>
                            <input type="text" class="form-control datetimepicker" id="filter_dari_tgl"
                                placeholder="Dari Tanggal" value="">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label>Sampai Tanggal</label>
                            <input type="text" class="form-control datetimepicker" id="filter_sampai_tgl"
                                placeholder="Sampai Tanggal" value="">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label>Eselon</label>
                            <select class="form-control select select2" id="filter_eselon" name="filter_eselon">
                                <option selected value="" disabled>Select Eselon</option>
                                @foreach ($eselon as $item)
                                    <option value="{{ $item->id }}""
                                        {{ old('eslon_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama . ' / ' . $item->deskripsi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 d-flex align-items-end">
                        <div class="form-group">
                            <button type="button" id="btn_filter" class="btn btn-primary me-2">
                                <i class="fa fa-search"></i> Filter
                            </button>
                            <button type="button" id="btn_reset" class="btn btn-secondary">
                                <i class="fa fa-refresh"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div> --}}
            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table comman-shadow">
                        <div class="card-body">
                            <div class="page-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="page-title">List Unit</h3>
                                    </div>
                                    <div class="col-auto text-end float-end ms-auto download-grp">
                                        <a href="{{ route('unit/list') }}" class="btn btn-outline-gray me-2 active">
                                            <i class="fa fa-list" aria-hidden="true"></i>
                                        </a>
                                        {{-- <a href="{{ route('student/grid') }}" class="btn btn-outline-gray me-2">
                                            <i class="fa fa-th" aria-hidden="true"></i>
                                        </a> --}}
                                        {{-- <a href="#" class="btn btn-outline-primary me-2"><i
                                                class="fas fa-download"></i> Download</a> --}}
                                        {{-- <div class="btn btn-group">
                                            <button type="button" class="btn btn-primary dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{ route('sp3/add/page') }}" class="dropdown-item"
                                                        href="#">Sp3 Billing</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('sp3/add/page/deposit') }}" class="dropdown-item"
                                                        href="#">Sp3 Deposit</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('sp3/add/page/mcu') }}" class="dropdown-item"
                                                        href="#">Sp3 MCU</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('sp3/add/page/tagihan-keluar') }}"
                                                        class="dropdown-item" href="#">Sp3 Pembayaran Tagihan Luar</a>
                                                </li>

                                            </ul>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-stripped table table-hover table-center mb-0" id="UnitList">
                                    <thead class="student-thread">
                                        <tr>

                                            <th>Nama</th>
                                            <th class="text-end">Action</th>
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
            $('.select2').select2();
            $('#UnitList').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                searching: true,
                order: [],
                ajax: {
                    url: "{{ route('get-units-data') }}",
                    cache: false,
                },
                columns: [{
                        data: 'nama',
                        name: 'nama',
                    },
                    {
                        data: 'modify',
                        name: 'modify',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>
@endsection

@endsection
