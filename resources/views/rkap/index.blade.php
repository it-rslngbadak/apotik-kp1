@extends('layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">RKAP Unit {{ $unit->periode }}</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('rkap/list', $unit->slug) }}">RKAP
                                        Unit</a></li>
                                <li class="breadcrumb-item active">All RKAP Units</li>
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
                                        <h3 class="page-title">List RKAP Unit {{ $unit->nama }}</h3>
                                    </div>
                                    <div class="col-auto text-end float-end ms-auto download-grp">
                                        <a href="{{ route('rkap/list', $unit->slug) }}"
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
                                <table class="table table-stripped table table-hover table-center mb-0" id="DataList">
                                    <thead class="student-thread">
                                        <tr>
                                            <th>Periode</th>
                                            <th>Pendapatan</th>
                                            <th>Biaya</th>
                                            <th>Status</th>
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

    {{-- Modal Form RKAP --}}
    <div class="modal fade" id="formCreate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="formCreateLabel">Buat RKAP</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formData">
                        <input type="hidden" id="rkap_id" name="id">
                        <div class="mb-3">
                            <label for="periode" class="col-form-label">Periode</label>
                            <input type="text" class="form-control" id="periode" name="periode">
                            <span class="text-danger error-periode"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Delete Program Unit --}}
    <div class="modal custom-modal fade" id="deleteData" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-header">
                        <h3>Delete RKAP</h3>
                        <p>Are you sure want to delete <strong id="delete_periode"></strong>?</p>
                    </div>
                    <div class="modal-btn delete-action">
                        <div class="row">
                            <form id="formDelete">
                                @csrf
                                <input type="hidden" name="id" id="delete_rkap_id" value="">
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

            let table = $('#DataList').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                searching: true,
                order: [],
                ajax: {
                    url: "{{ route('get-rkaps-data', $unit->slug) }}",
                    cache: false,
                },
                columns: [{
                        data: 'periode',
                        name: 'periode'
                    },
                    {
                        data: 'pendapatan',
                        name: 'pendapatan'
                    },
                    {
                        data: 'biaya',
                        name: 'biaya'
                    },
                    {
                        data: 'status',
                        name: 'status'
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
                $('#formData')[0].reset();
                $('#rkap_id').val('');
                $('#formCreateLabel').text('Buat RKAP');
            });

            // Buka modal Edit, isi data
            $(document).on('click', '.btn-edit-data', function() {
                $('#rkap_id').val($(this).data('id'));
                $('#periode').val($(this).data('periode'));
                $('#formCreateLabel').text('Edit Program');
                $('#formCreate').modal('show');
            });

            $('#formData').on('submit', function(e) {
                e.preventDefault(); // stop default browser submit / reload
                submitInput(); // panggil fungsi yang isinya AJAX kamu
            });

            $('#btnSubmit').on('click', function(e) {
                e.preventDefault();
                submitInput();
            });

            function submitInput() {
                $('.text-danger').text('');
                $('.form-control').removeClass('is-invalid');

                let id = $('#rkap_id').val();
                let url = id ?
                    "{{ url('rkap/' . $unit->slug . '/rkap') }}/" + id + "/update" :
                    "{{ route('rkap.store', $unit->slug) }}";
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: {
                        _token: "{{ csrf_token() }}",
                        periode: $('#periode').val(),
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
            $(document).on('click', '.btn-delete-data', function() {
                $('#delete_rkap_id').val($(this).data('id'));
                $('#delete_periode').text($(this).data('periode'));
            });

            // Submit form delete via AJAX
            $('#formDelete').on('submit', function(e) {
                e.preventDefault(); // stop native submit / reload

                let id = $('#delete_rkap_id').val();

                $.ajax({
                    url: "{{ url('rkap/' . $unit->slug . '/rkap') }}/" + id + "/delete",
                    method: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(res) {
                        $('#deleteData').modal('hide');
                        table.ajax.reload(null, false);
                        toastr.success(res.message);
                    },
                    error: function(xhr) {
                        $('#deleteRkap').modal('hide');
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
