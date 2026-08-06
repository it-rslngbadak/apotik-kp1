@extends('layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">SETORAN</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('list-customers') }}">Setoran</a></li>
                                <li class="breadcrumb-item active">All SETORAN</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            {{-- message --}}
            {!! Toastr::message() !!}
            <div id="shiftStatusBanner" class="mb-3"></div>
            <div class="col-auto text-end float-end ms-auto download-grp">

            </div>
            <div class="student-group-form mb-3">
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
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table comman-shadow">
                        <div class="card-body">
                            <div class="page-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="page-title">List Setoran</h3>
                                    </div>
                                    <div class="col-auto text-end float-end ms-auto download-grp">
                                        <button type="button" id="btn_add_setoran" class="btn btn-primary me-2"
                                            data-bs-toggle="modal" data-bs-target="#modalSetoran">
                                            <i class="fa fa-plus"></i> Tambah Setoran
                                        </button>
                                        <button type="button" id="btn_reload" class="btn btn-outline-gray me-2 active">
                                            <i class="fa fa-retweet" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-stripped table table-hover table-center mb-0" id="SetoranList"
                                    style="width: 100%">
                                    <thead class="student-thread">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Setoran</th>
                                            <th>Shift</th>
                                            <th>Kasir</th>
                                            <th>Selisih</th>
                                            {{-- <th>Status</th> --}}
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

        {{-- modal sp3 delete --}}
        {{-- <div class="modal custom-modal fade" id="delete" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-header">
                        <h3>Delete Sp3</h3>
                        <p>Are you sure want to delete?</p>
                    </div>
                    <div class="modal-btn delete-action">
                        <div class="row">
                            <form action="{{ route('sp3/delete') }}" method="POST">
                                @csrf
                                <input type="hidden" name="slug" class="e_slug" value="">
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
    </div> --}}
        <div class="modal fade" id="modalSetoran" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Setoran</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formSetoran">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" id="setoran_id" name="id" value="">
                            <div class="form-group mb-3">
                                <label>Uang Setoran</label>
                                <input type="number" class="form-control" id="edit_setoran" name="setoran"
                                    placeholder="Masukkan jumlah uang setoran" min="0">
                            </div>
                            <div class="form-group mb-3">
                                <label>Shift</label>
                                <select class="form-select" id="edit_shift" name="shift" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="PAGI">PAGI</option>
                                    <option value="SORE">SORE</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="row">
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary paid-continue-btn">Simpan</button>
                                </div>
                                <div class="col-6">
                                    <button data-bs-dismiss="modal" class="btn btn-primary paid-cancel-btn">Batal</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalExportInvoice" tabindex="-1" aria-labelledby="modalExportInvoiceLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalExportInvoiceLabel">Export Laporan Invoice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form id="formExportInvoice">
                        <div class="modal-body">

                            <div class="form-group mb-3">
                                <label>Dari Tanggal</label>
                                <input type="text" class="form-control datetimepicker" id="export_dari_tgl"
                                    placeholder="Pilih tanggal mulai" autocomplete="off">
                            </div>

                            <div class="form-group mb-2">
                                <label>Sampai Tanggal</label>
                                <input type="text" class="form-control datetimepicker" id="export_sampai_tgl"
                                    placeholder="Pilih tanggal akhir" autocomplete="off">
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-download"></i> Export Sekarang
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#SetoranList').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    searching: true,
                    ajax: {
                        url: "{{ route('get-data-setoran') }}",
                        cache: false,
                        data: function(d) {
                            d.dari_tgl = $('#filter_dari_tgl').val();
                            d.sampai_tgl = $('#filter_sampai_tgl').val();
                        }
                    },
                    columns: [{
                            data: 'tanggal',
                            name: 'tanggal'
                        },
                        {
                            data: 'setoran',
                            name: 'setoran'
                        },
                        {
                            data: 'shift',
                            name: 'shift'
                        },
                        {
                            data: 'kasir',
                            name: 'kasir'
                        },
                        {
                            data: 'selisih',
                            name: 'selisih'
                        },
                        {
                            data: 'modify',
                            name: 'modify',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });

                $('#btn_filter').on('click', function() {
                    $('#SetoranList').DataTable().ajax.reload();
                });

                $('#btn_reset').on('click', function() {
                    $('#filter_dari_tgl').val('');
                    $('#filter_sampai_tgl').val('');
                    $('#SetoranList').DataTable().ajax.reload(null, false);
                });

                $('#btn_reload').on('click', function() {
                    $('#SetoranList').DataTable().ajax.reload(null, false);
                    checkShiftStatus();
                });

                // buka modal untuk tambah
                $('#btn_add_setoran').on('click', function() {
                    $('#formSetoran')[0].reset();
                    $('#setoran_id').val('');
                });

                // buka modal untuk edit
                $(document).on('click', '.btn-edit-setoran', function() {
                    $('#setoran_id').val($(this).data('id'));
                    $('#edit_setoran').val($(this).data('setoran'));
                    $('#edit_shift').val($(this).data('shift'));
                    $('#modalSetoran').modal('show');
                });

                $('#formSetoran').on('submit', function(e) {
                    e.preventDefault();
                    let id = $('#setoran_id').val();
                    let url = id ?
                        "{{ url('setoran/update') }}/" + id :
                        "{{ route('store-setoran') }}";

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: $(this).serialize(),
                        dataType: 'json',
                        success: function(res) {
                            $('#modalSetoran').modal('hide');

                            let selisihHtml = '';
                            if (res.data) {
                                let selisih = res.data.selisih;
                                let totalTunai = res.data.total_tunai_customer;
                                let selisihClass = selisih === 0 ? 'text-success' : 'text-danger';
                                let selisihLabel = selisih === 0 ?
                                    'Rp 0 (pas)' :
                                    (selisih > 0 ? '+' : '-') + 'Rp ' + formatRupiah(Math.abs(
                                        selisih));

                                selisihHtml = `
                    <hr>
                    <div class="text-start">
                        <div>Total transaksi tunai (pembulatan) hari ini: <b>Rp ${formatRupiah(totalTunai)}</b></div>
                        <div>Selisih dengan setoran: <b class="${selisihClass}">${selisihLabel}</b></div>
                    </div>
                `;
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                html: res.message + selisihHtml
                            });

                            $('#SetoranList').DataTable().ajax.reload(null, false);
                            checkShiftStatus();
                        },
                        error: function(xhr) {
                            let msg = 'Terjadi kesalahan saat menyimpan data.';
                            if (xhr.status === 422 && xhr.responseJSON) {
                                msg = xhr.responseJSON.message ||
                                    Object.values(xhr.responseJSON.errors || {}).map(e => e[0])
                                    .join('<br>');
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                html: msg
                            });
                        }
                    });
                });

                checkShiftStatus();
                setInterval(checkShiftStatus, 60000); // cek ulang tiap 1 menit
            });

            function formatRupiah(angka) {
                angka = Math.abs(Number(angka) || 0);
                return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            function checkShiftStatus() {
                $.get("{{ route('setoran.shift-status') }}", function(res) {
                    let banner = '';
                    $.each(res, function(shift, info) {
                        if (info.can_input) {
                            banner += `<div class="alert alert-success py-2 mb-1">
                        <i class="fa fa-bell"></i> Sudah waktunya input setoran shift <b>${shift}</b> (dibuka pukul ${info.open_at}).
                    </div>`;
                        } else if (info.already_input) {
                            banner += `<div class="alert alert-secondary py-2 mb-1">
                        <i class="fa fa-check"></i> Setoran shift <b>${shift}</b> hari ini sudah diinput.
                    </div>`;
                        } else {
                            banner += `<div class="alert alert-light py-2 mb-1 text-muted">
                        <i class="fa fa-clock"></i> Setoran shift <b>${shift}</b> dibuka pukul ${info.open_at}.
                    </div>`;
                        }

                        $('#edit_shift option[value="' + shift + '"]').prop('disabled', !info.can_input);
                    });
                    $('#shiftStatusBanner').html(banner);
                });
            }

            function cetakStruk(id) {
                window.open("{{ url('setoran/cetak') }}/" + id, '_blank');
            }
        </script>
    @endsection
