@extends('layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">Program Unit {{ $rkap->unit->nama }} RKAP Tahun {{ $rkap->periode }}</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('program-unit/list', $rkap->slug) }}">Program
                                        Unit</a></li>
                                <li class="breadcrumb-item active">All Program Units</li>
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
                                        <h3 class="page-title">List Program Unit {{ $rkap->unit->nama }}</h3>
                                    </div>
                                    <div class="col-auto text-end float-end ms-auto download-grp">
                                        <a href="{{ route('program-unit/list', $rkap->slug) }}"
                                            class="btn btn-outline-gray me-2 active">
                                            <i class="fa fa-list" aria-hidden="true"></i>
                                        </a>
                                        {{-- <a href="{{ route('student/grid') }}" class="btn btn-outline-gray me-2">
                                            <i class="fa fa-th" aria-hidden="true"></i>
                                        </a> --}}
                                        {{-- <a href="#" class="btn btn-outline-primary me-2"><i
                                                class="fas fa-download"></i> Download</a> --}}
                                        <div class="btn btn-group">
                                            <button type="button" class="btn btn-primary dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{ route('create-regular-program', $rkap->slug) }}"
                                                        class="dropdown-item" href="#">Create Regular Program</a>
                                                </li>
                                                <li>
                                                    <a href="#" class="dropdown-item" id="openModal">
                                                        Create Work Program
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="filterBulan" class="form-label">Bulan</label>
                                    <select id="filterBulan" class="form-control select2" style="width: 100%;">
                                        <option value="">-- Pilih Bulan --</option>
                                        <option value="1">Januari</option>
                                        <option value="2">Februari</option>
                                        <option value="3">Maret</option>
                                        <option value="4">April</option>
                                        <option value="5">Mei</option>
                                        <option value="6">Juni</option>
                                        <option value="7">Juli</option>
                                        <option value="8">Agustus</option>
                                        <option value="9">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" id="btnFilter" class="btn btn-primary w-100">
                                        <i class="fa fa-search"></i> Filter
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-stripped table table-hover table-center mb-0"
                                    id="ProgramUnitList">
                                    <thead class="student-thread">
                                        <tr>
                                            <th>Nama Program</th>
                                            <th>Keterangan Program</th>
                                            <th>Kategori</th>
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

    {{-- Modal Form Program Unit --}}
    <div class="modal fade" id="formCreate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="formCreateLabel">Buat Program</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formProgramUnit">
                        <input type="hidden" id="program_id" name="id">
                        <div class="mb-3">
                            <label for="nama_program" class="col-form-label">Nama Program</label>
                            <input type="text" class="form-control" id="nama_program" name="nama_program">
                            <span class="text-danger error-nama_program"></span>
                        </div>
                        <div class="mb-3">
                            <label for="kategori" class="col-form-label">Kategori</label>
                            <select class="form-select" name="kategori" id="kategori">
                                <option value="" selected disabled>Pilih Kategori</option>
                                <option value="Regular">Regular</option>
                                <option value="Work Program">Work Program</option>
                            </select>
                            <span class="text-danger error-kategori"></span>
                        </div>

                        {{-- Wrapper bulan, disembunyikan default --}}
                        <div class="mb-3" id="wrapper_bulan" style="display: none;">
                            <label for="bulan" class="col-form-label">
                                Bulan <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" name="bulan" id="bulan">
                                <option value="" selected disabled>Pilih Bulan</option>
                                <option value="1">Januari</option>
                                <option value="2">Februari</option>
                                <option value="3">Maret</option>
                                <option value="4">April</option>
                                <option value="5">Mei</option>
                                <option value="6">Juni</option>
                                <option value="7">Juli</option>
                                <option value="8">Agustus</option>
                                <option value="9">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                            <span class="text-danger error-bulan"></span>
                        </div>
                        <div class="mb-3">
                            <label for="ket_program" class="col-form-label">Keterangan Program (Opsional)</label>
                            <textarea class="form-control" id="ket_program" name="ket_program"></textarea>
                            <span class="text-danger error-ket_program"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitProgram">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Delete Program Unit --}}
    <div class="modal custom-modal fade" id="deleteProgramUnit" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-header">
                        <h3>Delete Program Unit</h3>
                        <p>Are you sure want to delete <strong id="delete_program_nama"></strong>?</p>
                    </div>
                    <div class="modal-btn delete-action">
                        <div class="row">
                            <form id="formDeleteProgramUnit">
                                @csrf
                                <input type="hidden" name="id" id="delete_program_id" value="">
                                <div class="row">
                                    <div class="col-6">
                                        <button type="submit" class="btn btn-primary paid-continue-btn"
                                            style="width: 100%;">Delete</button>
                                    </div>
                                    <div class="col-6">
                                        <a data-bs-dismiss="modal" class="btn btn-primary paid-cancel-btn">Cancel
                                        </a>
                                    </div>
                                </div>
                            </form>
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

            let table = $('#ProgramUnitList').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                searching: true,
                order: [],
                ajax: {
                    url: "{{ route('get-program-units-data', $rkap->slug) }}",
                    cache: false,
                    data: function(d) {
                        d.filterBulan = $('#filterBulan').val();
                    }
                },
                columns: [{
                        data: 'nama_program',
                        name: 'nama_program'
                    },
                    {
                        data: 'ket_program',
                        name: 'ket_program'
                    },
                    {
                        data: 'kategori',
                        name: 'kategori'
                    },
                    {
                        data: 'modify',
                        name: 'modify',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $(document).on('click', '#openModal', function(e) {
                e.preventDefault();
                const modal = new bootstrap.Modal(document.getElementById('formCreate'));
                modal.show();
            });

            $('#btnFilter').on('click', function() {
                table.ajax.reload();
            });

            // Reset form saat modal dibuka untuk Create
            $('button[data-bs-target="#formCreate"]').on('click', function() {
                $('#formProgramUnit')[0].reset();
                $('#program_id').val('');
                $('#formCreateLabel').text('Buat Program');
            });

            // Buka modal Edit, isi data
            $(document).on('click', '.btn-edit-program', function() {
                $('.text-danger').text(''); // ⬅️ tambahkan ini
                $('.form-control').removeClass('is-invalid');
                $('#program_id').val($(this).data('id'));
                $('#nama_program').val($(this).data('nama'));
                $('#ket_program').val($(this).data('ket'));
                $('#kategori').val($(this).data('kategori'));
                $('#bulan').val($(this).data('bulan'));
                $('#formCreateLabel').text('Edit Program');
                $('#formCreate').modal('show');
            });

            $('#formProgramUnit').on('submit', function(e) {
                e.preventDefault(); // stop default browser submit / reload
                submitProgramUnit(); // panggil fungsi yang isinya AJAX kamu
            });

            $('#btnSubmitProgram').on('click', function(e) {
                e.preventDefault();
                submitProgramUnit();
            });

            $(document).on('change', '#kategori', function() {
                toggleBulanField($(this).val());
            });

            function toggleBulanField(kategori) {
                const $wrapperBulan = $('#wrapper_bulan');
                const $bulan = $('#bulan');

                if (kategori === 'Work Program') {
                    $wrapperBulan.show();
                    $bulan.prop('required', true);
                } else {
                    $wrapperBulan.hide();
                    $bulan.prop('required', false);
                    $bulan.val(''); // reset biar gak nyangkut value lama saat submit sebagai Regular
                }
            }

            // Pastikan state field bulan sesuai kondisi awal setiap modal dibuka
            // (misal saat edit data Work Program, atau reset saat create baru)
            $('#formCreate').on('shown.bs.modal', function() {
                toggleBulanField($('#kategori').val());
            });

            function submitProgramUnit() {
                $('.text-danger').text('');
                $('.form-control').removeClass('is-invalid');

                let id = $('#program_id').val();
                let url = id ?
                    "{{ url('rkap/' . $rkap->slug . '/program-unit') }}/" + id + "/update" :
                    "{{ route('program-unit.store', $rkap->slug) }}";
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: {
                        _token: "{{ csrf_token() }}",
                        nama_program: $('#nama_program').val(),
                        ket_program: $('#ket_program').val(),
                        kategori: $('#kategori').val(),
                        bulan: $('#bulan').val(),
                    },
                    success: function(res) {
                        $('#formCreate').modal('hide');
                        table.ajax.reload(null, false);
                        toastr.success(res.message);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(field, messages) {
                                $('#' + field).addClass('is-invalid');
                                $('.error-' + field).text(messages[0]);
                            });
                            toastr.error(xhr.responseJSON.message);
                        } else if (xhr.status === 404) {
                            toastr.error(xhr.responseJSON.message);
                        } else {
                            toastr.error('Terjadi kesalahan pada server');
                        }
                    }
                });
            }

            // Set id ke hidden input saat tombol delete diklik (modal delete kebuka)
            $(document).on('click', '.btn-delete-program', function() {
                $('#delete_program_id').val($(this).data('id'));
                $('#delete_program_nama').text($(this).data('nama'));
            });

            // Submit form delete via AJAX
            $('#formDeleteProgramUnit').on('submit', function(e) {
                e.preventDefault(); // stop native submit / reload

                let id = $('#delete_program_id').val();

                $.ajax({
                    url: "{{ url('rkap/' . $rkap->slug . '/program-unit') }}/" + id + "/delete",
                    method: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(res) {
                        $('#deleteProgramUnit').modal('hide');
                        table.ajax.reload(null, false);
                        toastr.success(res.message);
                    },
                    error: function(xhr) {
                        $('#deleteProgramUnit').modal('hide');
                        if (xhr.status === 404) {
                            toastr.error(xhr.responseJSON.message);
                        } else {
                            toastr.error('Gagal menghapus data');
                        }
                    }
                });
            });
        });
    </script>
@endsection

@endsection
