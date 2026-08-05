@extends('layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">KASIR</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('list-customers') }}">Customer</a></li>
                                <li class="breadcrumb-item active">All CUSTOMER</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            {{-- message --}}
            {!! Toastr::message() !!}
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
                                        <h3 class="page-title">List Customer</h3>
                                    </div>
                                    <div class="col-auto text-end float-end ms-auto download-grp">
                                        <button type="button" id="btn_reload" class="btn btn-outline-gray me-2 active">
                                            <i class="fa fa-retweet" aria-hidden="true"></i>
                                        </button>
                                        <a href="{{ route('list-customers') }}" class="btn btn-outline-gray me-2 active">
                                            <i class="fa fa-list" aria-hidden="true"></i>
                                        </a>
                                        @if (Session::get('role_name') === 'Admin Apotik' ||
                                                Session::get('role_name') === 'Admin' ||
                                                Session::get('role_name') === 'Super Admin')
                                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                                data-bs-target="#modalExportInvoice">
                                                <i class="fa fa-file-excel-o"></i> Export Laporan Invoice
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-stripped table table-hover table-center mb-0" id="CustomerList">
                                    <thead class="student-thread">
                                        <tr>
                                            <th>No Registrasi</th>
                                            <th>Tanggal Pelayanan</th>
                                            <th>No Telp</th>
                                            <th>Nama Pengunjung</th>
                                            <th>Metode Bayar</th>
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
        <div class="modal fade" id="modalEditCustomer" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Modal title</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formEditCustomer">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" id="edit_customer_id" name="id" value="">
                            <input type="hidden" id="edit_total" value="">
                            {{-- ================= RINCIAN TRANSAKSI OBAT (READ ONLY) ================= --}}
                            <div class="form-group mb-3">
                                <label>Rincian Transaksi Obat</label>
                                <div class="table-responsive"
                                    style="max-height: 220px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px;">
                                    <table class="table table-sm table-bordered mb-0" id="tabelRincianObat">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nama Obat</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-end">Harga</th>
                                                <th class="text-end">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody id="bodyRincianObat">
                                            {{-- diisi otomatis lewat JavaScript --}}
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="text-end"><strong>Total</strong></td>
                                                <td class="text-end"><strong id="totalRincianObat">Rp 0</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label>Metode Pembayaran</label>
                                <select class="form-select" id="edit_metode_pembayaran" name="metode_bayar">
                                    <option value="">-- Pilih --</option>
                                    <option value="QRIS">QRIS</option>
                                    <option value="TUNAI">TUNAI</option>
                                    <option value="TRANSFER">TRANSFER</option>
                                </select>
                            </div>
                            {{-- muncul hanya kalau metode bayar = TUNAI --}}
                            <div class="form-group mb-2" id="wrapper_uang_tunai" style="display:none;">
                                <label>Uang Tunai Diterima</label>
                                <input type="number" class="form-control" id="edit_uang_tunai" name="uang_tunai"
                                    placeholder="Masukkan jumlah uang tunai" min="0">
                            </div>

                            <div class="form-group mb-2" id="wrapper_kembalian" style="display:none;">
                                <label>Kembalian</label>
                                <input type="text" class="form-control" id="edit_kembalian" name="kembalian"
                                    readonly>
                            </div>

                            <div class="form-group mb-2">
                                <label>Nama Pengunjung</label>
                                <input type="text" class="form-control" id="edit_nama_customer" name="nama_customer">
                            </div>
                            <div class="form-group mb-2">
                                <label>No HP</label>
                                <input type="text" class="form-control" id="edit_no_hp" name="no_hp">
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

        <iframe id="iframeStruk" style="display:none;"></iframe>
    @section('script')
        {{-- get user all js --}}
        <script type="text/javascript">
            $(document).ready(function() {
                $('.select2').select2();
                $('#CustomerList').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    searching: true,
                    ajax: {
                        url: "{{ route('get-data-customers') }}",
                        cache: false,
                        data: function(d) {
                            d.dari_tgl = $('#filter_dari_tgl').val();
                            d.sampai_tgl = $('#filter_sampai_tgl').val();
                        }
                    },
                    columns: [{
                            data: 'no_registrasi',
                            name: 'no_registrasi',
                        },
                        {
                            data: 'tanggal_registrasi',
                            name: 'tanggal_registrasi'
                        },
                        {
                            data: 'no_hp',
                            name: 'no_hp'
                        },
                        {
                            data: 'nama_customer',
                            name: 'nama_customer'
                        },
                        {
                            data: 'metode_bayar',
                            name: 'metode_bayar'
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
                // Tombol Filter: reload datatable dengan nilai filter yang dipilih
                $('#btn_filter').on('click', function() {
                    $('#CustomerList').DataTable().ajax.reload(null, false);
                    // null = tidak ada callback, false = jangan kembali ke halaman 1
                });

                // Tombol Reset: kembalikan nilai filter ke default, lalu reload
                $('#btn_reset').on('click', function() {
                    $('#filter_dari_tgl').val('');
                    $('#filter_sampai_tgl').val('');
                    $('#CustomerList').DataTable().ajax.reload(null, false);
                });

                // Tombol Reload (AJAX)
                $('#btn_reload').on('click', function() {
                    var $btn = $(this);
                    $btn.prop('disabled', true);
                    var iconClass = $btn.find('i').attr('class');
                    $btn.find('i').removeClass(iconClass).addClass('fa fa-spinner fa-spin');

                    $.ajax({
                        url: "{{ route('reload-customers') }}",
                        type: 'GET',
                        dataType: 'json',
                        success: function(res) {
                            if (res.status === 'success') {
                                toastr.success(res.message);
                                $('#CustomerList').DataTable().ajax.reload(null, false);
                            } else {
                                toastr.error(res.message);
                            }
                        },
                        error: function() {
                            toastr.error('Terjadi kesalahan saat reload data');
                        },
                        complete: function() {
                            $btn.prop('disabled', false);
                            $btn.find('i').removeClass('fa fa-spinner fa-spin').addClass(iconClass);
                        }
                    });
                });

                // panggil toggle setiap kali metode bayar diganti
                $('#edit_metode_pembayaran').on('change', function() {
                    toggleFieldTunai();
                });

                // hitung ulang kembalian setiap kali uang tunai diketik
                $('#edit_uang_tunai').on('input', function() {
                    hitungKembalian();
                });

                // saat modal edit dibuka, isi semua field termasuk total & tunai
                $(document).on('click', '.btn-edit-customer', function() {
                    var $btn = $(this);
                    $('#edit_customer_id').val($btn.data('id'));
                    $('#edit_nama_customer').val($btn.data('nama_customer'));
                    $('#edit_no_hp').val($btn.data('no_hp'));
                    $('#edit_metode_pembayaran').val($btn.data('metode_bayar')).trigger('change');
                    $('#edit_total').val($btn.data('total'));
                    $('#edit_uang_tunai').val($btn.data('uang_tunai'));
                    $('#edit_kembalian').val($btn.data('kembalian'));

                    renderRincianObat($btn.data('items'));

                    toggleFieldTunai
                        (); // pastikan field muncul/sembunyi sesuai metode bayar yang sudah tersimpan
                    $('#modalEditCustomer').modal('show');
                });

                // Submit form edit via AJAX
                $('#formEditCustomer').on('submit', function(e) {
                    e.preventDefault();
                    var id = $('#edit_customer_id').val();

                    $.ajax({
                        url: "{{ route('update-customer') }}",
                        type: 'POST',
                        data: $(this).serialize(),
                        dataType: 'json',
                        success: function(res) {
                            if (res.status === 'success') {
                                toastr.success(res.message);
                                $('#modalEditCustomer').modal('hide');
                                $('#CustomerList').DataTable().ajax.reload(null, false);
                            } else {
                                toastr.error(res.message);
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                var errors = xhr.responseJSON.errors;
                                var msg = Object.values(errors).map(e => e[0]).join('<br>');
                                toastr.error(msg);
                            } else {
                                toastr.error('Terjadi kesalahan saat menyimpan data');
                            }
                        }
                    });
                });

                $('#formExportInvoice').on('submit', function(e) {
                    e.preventDefault();

                    const dariTgl = $('#export_dari_tgl').val();
                    const sampaiTgl = $('#export_sampai_tgl').val();

                    if (!dariTgl || !sampaiTgl) {
                        toastr.error('Silakan pilih rentang tanggal terlebih dahulu');
                        return;
                    }

                    if (dariTgl > sampaiTgl) {
                        toastr.error('Tanggal mulai tidak boleh lebih besar dari tanggal akhir');
                        return;
                    }

                    const url = "{{ route('laporan.invoice.export') }}" +
                        "?dari_tanggal=" + encodeURIComponent(dariTgl) +
                        "&sampai_tanggal=" + encodeURIComponent(sampaiTgl);

                    window.location.href = url;

                    $('#modalExportInvoice').modal('hide');
                });
            });

            function cetakStruk(id) {
                const iframe = document.getElementById('iframeStruk');

                // muat halaman struk ke dalam iframe tersembunyi
                iframe.src = '/kasir/struk/' + id;

                // begitu iframe selesai dimuat, langsung panggil print pada iframe itu
                iframe.onload = function() {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                };
            }

            function toggleFieldTunai() {
                const metode = $('#edit_metode_pembayaran').val();
                if (metode === 'TUNAI') {
                    $('#wrapper_uang_tunai').show();
                    $('#wrapper_kembalian').show();
                } else {
                    $('#wrapper_uang_tunai').hide();
                    $('#wrapper_kembalian').hide();
                    $('#edit_uang_tunai').val('');
                    $('#edit_kembalian').val('');
                }
            }

            function hitungKembalian() {
                const total = parseFloat($('#edit_total').val()) || 0;
                const uangTunai = parseFloat($('#edit_uang_tunai').val()) || 0;
                const kembalian = uangTunai - total;

                $('#edit_kembalian').val(
                    kembalian >= 0 ? kembalian.toLocaleString('id-ID') : 'Kurang ' + Math.abs(kembalian).toLocaleString(
                        'id-ID')
                );
            }

            function renderRincianObat(itemsJson) {
                const $tbody = $('#bodyRincianObat');
                $tbody.empty();

                let items = [];
                try {
                    items = typeof itemsJson === 'string' ? JSON.parse(itemsJson) : itemsJson;
                } catch (e) {
                    items = [];
                }

                if (!items || items.length === 0) {
                    $tbody.append('<tr><td colspan="4" class="text-center text-muted">Tidak ada data obat</td></tr>');
                    $('#totalRincianObat').text('Rp 0');
                    return;
                }

                let total = 0;
                console.log(items);

                items.forEach(function(item) {
                    total += item.sub_total;
                    $tbody.append(`
                    <tr>
                        <td>${item.nama_obat}</td>
                        <td class="text-center">${item.jumlah}</td>
                        <td class="text-end">${formatRupiah(item.harga_jual)}</td>
                        <td class="text-end">${formatRupiah(item.sub_total)}</td>
                    </tr>
                    `);
                });

                $('#totalRincianObat').text('Rp ' + formatRupiah(total));
            }

            function formatRupiah(angka) {
                return Number(angka).toLocaleString('id-ID');
            }
        </script>
    @endsection

@endsection
