@extends('layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">Rincian Program {{ $program->nama_program }}</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('coa/list', $program->slug) }}">Rincian
                                        Program
                                        Unit</a></li>
                                <li class="breadcrumb-item active">All Rincian Program Units</li>
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
                                        <h3 class="page-title">List Rincian Program {{ $program->nama_program }}</h3>
                                    </div>
                                    <div class="col-auto text-end float-end ms-auto download-grp">
                                        <a href="{{ route('coa/list', $program->slug) }}"
                                            class="btn btn-outline-gray me-2 active">
                                            <i class="fa fa-list" aria-hidden="true"></i>
                                        </a>
                                        {{-- <a href="{{ route('student/grid') }}" class="btn btn-outline-gray me-2">
                                            <i class="fa fa-th" aria-hidden="true"></i>
                                        </a> --}}
                                        {{-- <a href="#" class="btn btn-outline-primary me-2"><i
                                                class="fas fa-download"></i> Download</a> --}}
                                        <div class="btn btn-group">
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#formCreate">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-stripped table table-hover table-center mb-0" id="CoaList">
                                    <thead class="student-thread">
                                        <tr>
                                            <th>Kode COA</th>
                                            <th>Eselon</th>
                                            <th>Keterangan COA</th>
                                            <th>Uraian</th>
                                            <th>Harga Satuan</th>
                                            <th>Jumlah</th>
                                            <th>Satuan</th>
                                            <th>Perkiraan</th>
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

    {{-- Modal Form COA --}}
    <div class="modal fade" id="formCreate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="formCreateLabel">Buat COA</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formProgramUnit">
                        <input type="hidden" id="program_unit_id" name="program_unit_id" value="{{ $program->id }}">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis_coa" id="pendapatan"
                                    value="Pendapatan">
                                <label class="form-check-label" for="pendapatan">
                                    Pendapatan
                                </label>
                                <input class="form-check-input" type="radio" name="jenis_coa" id="biaya"
                                    value="Biaya">
                                <label class="form-check-label" for="biaya">
                                    Biaya
                                </label>
                            </div>
                            <span class="text-danger error-jenis_coa"></span>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="kategori" id="tindakan"
                                    value="Tindakan">
                                <label class="form-check-label" for="tindakan">
                                    Tindakan
                                </label>
                                <input class="form-check-input" type="radio" name="kategori" id="farmalkes"
                                    value="Farmalkes">
                                <label class="form-check-label" for="farmalkes">
                                    Farmalkes
                                </label>
                                <input class="form-check-input" type="radio" name="kategori" id="umum"
                                    value="Umum">
                                <label class="form-check-label" for="umum">
                                    Umum
                                </label>
                            </div>
                            <span class="text-danger error-kategori"></span>
                        </div>

                        {{-- Jika jenis coa adalah pendapatan maka tindakan dan atau farmalkes itu wajib ada eselonnya --}}
                        <div class="mb-3">
                            <label for="eselon" class="col-form-label">Eselon</label>
                            <select class="form-select" aria-label="Default select example" id="eselon"
                                name="eselon">
                                <option selected>Pilih Jenis Eselon</option>
                                <option value="Cash">Cash</option>
                                <option value="Asuransi">Asuransi</option>
                                <option value="Yayasan">Yayasan</option>
                                <option value="Perusahaan">Perusahaan</option>
                            </select>
                        </div>

                        {{-- input Select 2 untuk searching datanya berdasarkan jenis --}}
                        <div class="mb-3">
                            <label for="desc_transaksi" class="col-form-label">Uraian Pekerjaan</label>
                            <input type="text" class="form-control" id="desc_transaksi" name="desc_transaksi">
                            <span class="text-danger error-desc_transaksi"></span>
                        </div>

                        jika uraian sudah terisi maka jumlah dan harga satuan autofill berdasarkan data masternya.
                        <div class="mb-3">
                            <label for="jumlah" class="col-form-label">Jumlah</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah">
                            <span class="text-danger error-jumlah"></span>
                        </div>
                        <div class="mb-3">
                            <label for="satuan" class="col-form-label">Satuan</label>
                            <input type="number" class="form-control" id="satuan" name="satuan">
                            <span class="text-danger error-satuan"></span>
                        </div>
                        <div class="mb-3">
                            <label for="harga_satuan" class="col-form-label">Harga Satuan</label>
                            <input type="number" class="form-control" id="harga_satuan" name="harga_satuan">
                            <span class="text-danger error-harga_satuan"></span>
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

            let table = $('#CoaList').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                searching: true,
                order: [],
                ajax: {
                    url: "{{ route('get-coas-data', $program->slug) }}",
                    cache: false,
                },
                columns: [{
                        data: 'coa',
                        name: 'coa'
                    },
                    {
                        data: 'eselon',
                        name: 'eselon'
                    },
                    {
                        data: 'ket_coa',
                        name: 'ket_coa'
                    },
                    {
                        data: 'desc_transaksi',
                        name: 'desc_transaksi'
                    },
                    {
                        data: 'harga_satuan',
                        name: 'harga_satuan'
                    },
                    {
                        data: 'jumlah',
                        name: 'jumlah'
                    },
                    {
                        data: 'satuan',
                        name: 'satuan'
                    },
                    {
                        data: 'perkiraan',
                        name: 'perkiraan'
                    },
                    {
                        data: 'modify',
                        name: 'modify',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // Reset form saat modal dibuka untuk Create
            $('button[data-bs-target="#formCreate"]').on('click', function() {
                $('#formProgramUnit')[0].reset();
                $('#program_id').val('');
                $('#formCreateLabel').text('Buat Program');
            });

            // Buka modal Edit, isi data
            $(document).on('click', '.btn-edit-program', function() {
                $('#program_id').val($(this).data('id'));
                $('#nama_program').val($(this).data('nama'));
                $('#ket_program').val($(this).data('ket'));
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

            function submitProgramUnit() {
                $('.text-danger').text('');
                $('.form-control').removeClass('is-invalid');

                let id = $('#program_id').val();
                let url = id ?
                    "{{ url('rkap/' . $program->slug . '/coa') }}/" + id + "/update" :
                    "{{ route('coa.store', $program->slug) }}";
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: {
                        _token: "{{ csrf_token() }}",
                        nama_program: $('#nama_program').val(),
                        ket_program: $('#ket_program').val(),
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
                    url: "{{ url('rkap/' . $program->slug . '/coa') }}/" + id + "/delete",
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
