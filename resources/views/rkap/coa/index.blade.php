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
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#referensiKodeTransaksi">
                                            Kode Transaksi
                                        </button>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#referensiPendapatan">
                                            Ref Pendapatan
                                        </button>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#referensiBiaya">
                                            Ref Biaya
                                        </button>
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
                                            <th>Kode Transaksi</th>
                                            <th>Eselon</th>
                                            <th>Keterangan Transaksi</th>
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
                    <h1 class="modal-title fs-5" id="formCreateLabel">Buat Transaksi</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formCoa" x-data="{ jenisCoa: null, kategori: null }">
                        <input type="hidden" id="coa_id" name="coa_id">
                        <input type="hidden" id="program_unit_id" name="program_unit_id" value="{{ $program->id }}">

                        {{-- Jenis COA --}}
                        <div class="mb-3">
                            <label class="form-label d-block">Jenis Kode Transkasi</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis_coa" id="pendapatan"
                                    value="Pendapatan" x-model="jenisCoa">
                                <label class="form-check-label" for="pendapatan">Pendapatan</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis_coa" id="biaya"
                                    value="Biaya" x-model="jenisCoa">
                                <label class="form-check-label" for="biaya">Biaya</label>
                            </div>
                            <span class="text-danger error-jenis_coa"></span>
                        </div>

                        {{-- Kategori --}}
                        <div class="mb-3">
                            <label class="form-label d-block">Kategori</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="kategori" id="tindakan"
                                    value="Tindakan" x-model="kategori">
                                <label class="form-check-label" for="tindakan">Tindakan</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="kategori" id="farmalkes"
                                    value="Farmalkes" x-model="kategori">
                                <label class="form-check-label" for="farmalkes">Farmalkes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="kategori" id="umum"
                                    value="Umum" x-model="kategori">
                                <label class="form-check-label" for="umum">Umum</label>
                            </div>
                            <span class="text-danger error-kategori"></span>
                        </div>

                        <div class="row" x-show="kategori === 'Tindakan'" x-cloak>
                            <div class="mb-3 col-6">
                                <label for="eselon" class="col-form-label">Eselon</label>
                                <select class="form-select" id="eselon" name="eselon"
                                    :required="jenisCoa === 'Pendapatan' && kategori === 'Tindakan'">
                                    <option value="">Pilih Jenis Eselon</option>
                                    <option value="TUNAI">CASH/TUNAI</option>
                                    <option value="ASURANSI">ASURANSI</option>
                                    <option value="YAYASAN">YAYASAN</option>
                                    <option value="PERUSAHAAN">PERUSAHAAN</option>
                                    <option value="INHEALTH">INHEALTH</option>
                                    <option value="POTONG GAJI PEKERJA RS LNG BADAK">Potong Gaji Pekerja RS</option>
                                </select>
                                <span class="text-danger error-eselon"></span>
                            </div>
                            <div class="mb-3 col-6">
                                <label for="jenis_tarif" class="col-form-label">Jenis Tarif</label>
                                <select class="form-select" id="jenis_tarif" name="jenis_tarif">
                                    <option value="">Pilih Jenis Tarif</option>
                                    <option value="tarif_kamar">Tarif Kamar</option>
                                    <option value="tarif_rj">Tarif Rawat Jalan</option>
                                    <option value="tarif_ugd">Tarif UGD</option>
                                    <option value="tarif_kls_3">Tarif Kelas 3</option>
                                    <option value="tarif_kls_2">Tarif Kelas 2</option>
                                    <option value="tarif_kls_1">Tarif Kelas 1</option>
                                    <option value="tarif_kls_vip">Tarif VIP</option>
                                    <option value="tarif_kls_icu">Tarif ICU</option>
                                    <option value="tarif_kls_isolasi">Tarif Isolasi</option>
                                </select>
                                <span class="text-danger error-jenis_tarif"></span>
                            </div>
                        </div>

                        {{-- Uraian Pekerjaan: select2 tags, sumber tergantung kategori --}}
                        <div class="mb-3">
                            <label for="desc_transaksi" class="col-form-label">Uraian Pekerjaan</label>
                            <select class="form-select" id="desc_transaksi" name="desc_transaksi"
                                style="width:100%"></select>
                            <span class="text-danger error-desc_transaksi"></span>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-4">
                                <label for="jumlah" class="col-form-label">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" value="1"
                                    min="1">
                                <span class="text-danger error-jumlah"></span>
                            </div>
                            <div class="mb-3 col-4">
                                <label for="satuan" class="col-form-label">Satuan</label>
                                <input type="text" class="form-control" id="satuan" name="satuan">
                                <span class="text-danger error-satuan"></span>
                            </div>
                            <div class="mb-3 col-4">
                                <label for="harga_satuan" class="col-form-label">Harga Satuan</label>
                                <input type="number" class="form-control" id="harga_satuan" name="harga_satuan">
                                <span class="text-danger error-harga_satuan"></span>
                            </div>
                        </div>

                        {{-- COA / Kode Transaksi --}}
                        <div class="mb-3">
                            <label for="kode_transaksi_id" class="col-form-label">Kode Transaksi</label>
                            <select class="form-select" id="kode_transaksi_id" name="kode_transaksi_id"
                                style="width:100%"></select>
                            <span class="text-danger error-kode_transaksi_id"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitCoa">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Delete COA --}}
    <div class="modal custom-modal fade" id="deleteProgramUnit" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-header">
                        <h3>Delete COA</h3>
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

    <!-- Modal Referensi Biaya -->
    <div class="modal fade" id="referensiBiaya" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Referensi Biaya</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Form Filter --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="filterBiayaUnit" class="form-label">Unit</label>
                            <select id="filterBiayaUnit" class="form-control select2" style="width: 100%;">
                                <option value="">-- Pilih Unit --</option>
                                @foreach ($biayaUnits as $unit)
                                    <option value="{{ $unit->nama_unit }}">{{ $unit->nama_unit }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" id="btnFilterRefBiaya" class="btn btn-primary w-100">
                                <i class="fa fa-search"></i> Filter
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-stripped table table-hover table-center mb-0 w-100" id="RefBiayaList">
                            <thead class="student-thread">
                                <tr>
                                    <th>Nama Transaksi</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Referensi Pendapatan -->
    <div class="modal fade" id="referensiPendapatan" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Referensi Pendapatan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Form Filter --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="filterPendapatanUnit" class="form-label">Unit</label>
                            <select id="filterPendapatanUnit" class="form-control select2" style="width: 100%;">
                                <option value="">-- Pilih Unit --</option>
                                @foreach ($pendapatanUnits as $unit)
                                    <option value="{{ $unit->nama_unit }}">{{ $unit->nama_unit }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" id="btnFilterRefPendapatan" class="btn btn-primary w-100">
                                <i class="fa fa-search"></i> Filter
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-stripped table table-hover table-center mb-0 w-100"
                            id="RefPendapatanList">
                            <thead class="student-thread">
                                <tr>
                                    <th>COA</th>
                                    <th>Nama Transaksi</th>
                                    <th>Cara Bayar</th>
                                    <th>Jumlah</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Referensi Kode Transaksi/COA -->
    <div class="modal fade" id="referensiKodeTransaksi" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Referensi Kode Transaksi</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Form Filter --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="filterJenisKodeTransaksi" class="form-label">Jenis Kode Transaksi</label>
                            <select id="filterJenisKodeTransaksi" class="form-control select2" style="width: 100%;">
                                <option value="">-- Pilih Jenis Kode Transaksi --</option>
                                <option value="Pendapatan">Pendapatan</option>
                                <option value="Biaya">Biaya</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" id="btnFilterKodeTransaksi" class="btn btn-primary w-100">
                                <i class="fa fa-search"></i> Filter
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-stripped table table-hover table-center mb-0 w-100"
                            id="RefKodeTransaksiList">
                            <thead class="student-thread">
                                <tr>
                                    <th>Kode Transaksi</th>
                                    <th>Nama Transaksi</th>
                                    <th>Definisi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@section('script')
    <script type="text/javascript">
        $(document).ready(function() {

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

            let tableRefBiaya = $('#RefBiayaList').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                searching: true,
                order: [],
                ajax: {
                    url: "{{ route('get-biayas-data') }}",
                    cache: false,
                    data: function(d) {
                        d.nama_unit = $('#filterBiayaUnit').val();
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
            $('#btnFilterRefBiaya').on('click', function() {
                tableRefBiaya.ajax.reload();
            });

            let tableRefPendapatan = $('#RefPendapatanList').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                searching: true,
                order: [],
                ajax: {
                    url: "{{ route('get-pendapatans-data') }}",
                    cache: false,
                    data: function(d) {
                        d.nama_unit = $('#filterPendapatanUnit').val();
                    }
                },
                columns: [{
                        data: 'coa_pendapatan',
                        name: 'coa_pendapatan'
                    },
                    {
                        data: 'nama_transaksi',
                        name: 'nama_transaksi'
                    },
                    {
                        data: 'cara_bayar',
                        name: 'cara_bayar'
                    },
                    {
                        data: 'jumlah',
                        name: 'jumlah'
                    },
                    {
                        data: 'total',
                        name: 'total'
                    },
                ]
            });
            // Reload data saat tombol filter diklik
            $('#btnFilterRefPendapatan').on('click', function() {
                tableRefPendapatan.ajax.reload();
            });

            let tableRefKodeTransaksi = $('#RefKodeTransaksiList').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                searching: true,
                order: [],
                ajax: {
                    url: "{{ route('get-kode-transaksi-data') }}",
                    cache: false,
                    data: function(d) {
                        d.jenis_kode = $('#filterJenisKodeTransaksi').val();
                    }
                },
                columns: [{
                        data: 'kode',
                        name: 'kode'
                    },
                    {
                        data: 'nama_transaksi',
                        name: 'nama_transaksi'
                    },
                    {
                        data: 'desc',
                        name: 'desc'
                    },
                ]
            });
            // Reload data saat tombol filter diklik
            $('#btnFilterKodeTransaksi').on('click', function() {
                tableRefKodeTransaksi.ajax.reload();
            });


            // ================== HELPER SELECT2 DINAMIS ==================

            function destroySelect2(selector) {
                if ($(selector).hasClass('select2-hidden-accessible')) {
                    $(selector).select2('destroy');
                }
            }

            function initUraianSelect2(kategori) {
                destroySelect2('#desc_transaksi');
                $('#desc_transaksi').empty();
                if (!kategori) return;

                const urlMap = {
                    'Tindakan': "{{ route('master-tindakan.search') }}",
                    'Farmalkes': "{{ route('master-farmalkes.search') }}",
                    'Umum': "{{ route('master-umum.search') }}",
                };

                $('#desc_transaksi').select2({
                    dropdownParent: $('#formCreate'),
                    tags: true, // izinkan input manual di luar referensi
                    placeholder: 'Ketik atau pilih uraian pekerjaan',
                    minimumInputLength: 1,
                    ajax: {
                        url: urlMap[kategori],
                        dataType: 'json',
                        delay: 300,
                        data: function(params) {
                            console.log(params);
                            return {
                                q: params.term,
                                eselon: $('#eselon').val(),
                                jenis_tarif: $('#jenis_tarif').val(), // hanya relevan utk Tindakan
                            };
                        },
                        processResults: function(data) {
                            console.log(data);
                            return {
                                results: data
                            };
                        }
                    },
                });
            }

            function initKodeTransaksiSelect2(jenisCoa, kategori) {
                destroySelect2('#kode_transaksi_id');
                $('#kode_transaksi_id').empty();
                if (!jenisCoa) return;

                $('#kode_transaksi_id').select2({
                    dropdownParent: $('#formCreate'),
                    placeholder: 'Pilih COA',
                    minimumInputLength: 0,
                    ajax: {
                        url: "{{ route('kode-transaksi.search') }}",
                        dataType: 'json',
                        delay: 300,
                        data: function(params) {
                            return {
                                q: params.term,
                                jenis_kode: jenisCoa,
                                kategori: kategori,
                                program_unit_id: $('#program_unit_id').val(),
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            };
                        }
                    },
                });
            }

            function resetDynamicFields() {
                $('#eselon').closest('.row').hide(); // sembunyikan lagi default
                $('#eselon').val('').prop('required', false);
                $('#jenis_tarif').val('');
                destroySelect2('#desc_transaksi');
                $('#desc_transaksi').empty();
                destroySelect2('#kode_transaksi_id');
                $('#kode_transaksi_id').empty();
                $('#jumlah').val(1);
                $('#satuan').val('');
                $('#harga_satuan').val('');
            }

            // ================== EVENT LISTENERS FORM DINAMIS ==================

            // ganti jenis_coa -> re-init COA select
            $(document).on('change', 'input[name="jenis_coa"]', function() {
                const jenisCoa = $(this).val();
                const kategori = $('input[name="kategori"]:checked').val();

                if (kategori === 'Tindakan') {
                    $('#eselon').prop('required', jenisCoa === 'Pendapatan');
                }

                initKodeTransaksiSelect2(jenisCoa, kategori);
            });

            // ganti kategori -> reset & re-init uraian pekerjaan + COA select
            $(document).on('change', 'input[name="kategori"]', function() {
                const kategori = $(this).val();
                const jenisCoa = $('input[name="jenis_coa"]:checked').val();
                const $eselonJenisTarifWrapper = $('#eselon').closest(
                    '.row'); // wrapper div yg tadinya pakai x-show

                if (kategori === 'Tindakan') {
                    $eselonJenisTarifWrapper.show();
                    $('#eselon').prop('required', jenisCoa === 'Pendapatan');
                } else {
                    $eselonJenisTarifWrapper.hide();
                    $('#eselon').prop('required', false).val('');
                    $('#jenis_tarif').val('');
                }

                initUraianSelect2(kategori);
                initKodeTransaksiSelect2(jenisCoa, kategori);
                $('#jumlah').val(1);
                $('#satuan').val('');
                $('#harga_satuan').val('');
            });

            // eselon & jenis_tarif dua-duanya trigger ulang pencarian uraian (khusus Tindakan)
            $(document).on('change', '#eselon, #jenis_tarif', function() {
                if ($('input[name="kategori"]:checked').val() === 'Tindakan') {
                    $('#desc_transaksi').val(null).trigger('change');
                    initUraianSelect2('Tindakan');
                }
            });

            // autofill saat pilih dari referensi (bukan input manual/tags)
            $(document).on('select2:select', '#desc_transaksi', function(e) {
                const data = e.params.data;

                if (data.newTag) {
                    $('#jumlah').val(1);
                    $('#satuan').val('');
                    $('#harga_satuan').val('');
                    return;
                }

                $('#jumlah').val(data.jumlah ?? 1);
                $('#satuan').val(data.satuan ?? '');
                $('#harga_satuan').val(data.harga_satuan ?? '');
            });

            // ================== MODAL CREATE / EDIT ==================

            // Reset form saat modal dibuka untuk Create
            $('button[data-bs-target="#formCreate"]').on('click', function() {
                $('#formCoa')[0].reset();
                $('#coa_id').val(''); // pastikan ada hidden input #coa_id di form untuk mode edit
                $('#formCreateLabel').text('Buat Transaksi');
                resetDynamicFields();
            });

            // Buka modal Edit, isi data (sesuaikan class tombol & data-attribute dengan tombol "modify" kamu)
            $(document).on('click', '.btn-edit-coa', function() {
                const id = $(this).data('id');
                console.log(id);

                $('#coa_id').val(id);
                $('#formCreateLabel').text('Edit Transaksi');

                $('input[name="jenis_coa"][value="' + $(this).data('jenis_coa') + '"]').prop('checked',
                    true);
                $('input[name="kategori"][value="' + $(this).data('kategori') + '"]').prop('checked', true)
                    .trigger('change');
                $('#eselon').val($(this).data('eselon'));
                $('#jenis_tarif').val($(this).data('jenis_tarif'));
                $('#jumlah').val($(this).data('jumlah'));
                $('#satuan').val($(this).data('satuan'));
                $('#harga_satuan').val($(this).data('harga_satuan'));
                $('#jenis_coa').val($(this).data('jenis_coa'));
                $('#kategori').val($(this).data('kategori'));
                const coaOption = new Option($(this).data('coa_text'), $(this).data('kode_transaksi_id'),
                    true, true);
                $('#kode_transaksi_id').append(coaOption).trigger('change');

                const descOption = new Option($(this).data('desc_transaksi'), $(this).data(
                    'desc_transaksi'), true, true);
                $('#desc_transaksi').append(descOption).trigger('change');

                $('#eselon').val($(this).data('eselon'));

                $('#formCreate').modal('show');
            });

            // ================== SUBMIT CREATE / UPDATE ==================

            $('#formCoa').on('submit', function(e) {
                e.preventDefault();
                submitCoa();
            });

            $('#btnSubmitCoa').on('click', function(e) {
                e.preventDefault();
                submitCoa();
            });

            function submitCoa() {
                $('.text-danger').text('');
                $('.form-control, .form-select').removeClass('is-invalid');

                let id = $('#coa_id').val();
                console.log(id);
                let url = id ?
                    "{{ url('rkap/' . $program->slug . '/coa') }}/" + id + "/update" :
                    "{{ route('coa.store', $program->slug) }}";
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: {
                        _token: "{{ csrf_token() }}",
                        program_unit_id: $('#program_unit_id').val(),
                        jenis_coa: $('input[name="jenis_coa"]:checked').val(),
                        kategori: $('input[name="kategori"]:checked').val(),
                        eselon: $('#eselon').val(),
                        jenis_tarif: $('#jenis_tarif').val(),
                        desc_transaksi: $('#desc_transaksi').val(),
                        jumlah: $('#jumlah').val(),
                        satuan: $('#satuan').val(),
                        harga_satuan: $('#harga_satuan').val(),
                        kode_transaksi_id: $('#kode_transaksi_id').val(),
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
                            toastr.error('Terjadi kesalahan pada server ' + xhr.responseJSON.message);
                        }
                    }
                });
            }

            // ================== DELETE (pakai modal & id asli: deleteProgramUnit) ==================

            $(document).on('click', '.btn-delete-coa', function() {
                $('#delete_program_id').val($(this).data('id'));
                $('#delete_program_nama').text($(this).data('nama'));
            });

            $('#formDeleteProgramUnit').on('submit', function(e) {
                e.preventDefault();

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
