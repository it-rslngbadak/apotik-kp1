@extends('layouts.master')
@section('content')
    {{-- {!! Toastr::message() !!} --}}
    <div class="page-wrapper">
        <div class="content container-fluid">

            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">Buat Reguler Program {{ $rkap->unit->nama }}</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a
                                        href="{{ route('create-regular-program', $rkap->slug) }}">Program Unit</a></li>
                                <li class="breadcrumb-item active">Buat Program Unit</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            {{-- message --}}
            {!! Toastr::message() !!}
            <div class="row">
                <div class="col-sm-12">
                    <div class="card comman-shadow">
                        <div class="card-body">
                            <form action="{{ route('program-unit-regular.store', $rkap->slug) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="form-title student-info">Form Tambah Regular Program
                                        </h5>
                                    </div>
                                    <input type="hidden" name="kategori" value="Regular">
                                    <div class="col-12">
                                        <div class="form-group local-forms">
                                            <label>Nama Program <span class="login-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('nama_program') is-invalid @enderror"
                                                name="nama_program" placeholder="Enter Nama Program"
                                                value="{{ old('nama_program') }}" required>
                                            @error('nama_program')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <div class="form-group local-forms">
                                            <label for="keterangan" class="form-label">Keterangan (Optional)</label>
                                            <textarea class="form-control" id="keterangan" name="keterangan" rows="5"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end">
                                        <div class="student-submit">
                                            <button type="submit" class="btn btn-primary">Next <i
                                                    class="fas fa-arrow-right"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endsection
