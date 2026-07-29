@extends('layouts.master')
@section('content')
    {{-- message --}}
    {!! Toastr::message() !!}
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">Welcome {{ Session::get('name') }}!</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                                <li class="breadcrumb-item active">{{ Session::get('name') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-sm-6 col-12 d-flex">
                    <div class="card bg-comman w-100">
                        <div class="card-body">
                            <div class="db-widgets d-flex justify-content-between align-items-center">
                                <div class="db-info">
                                    <h6>Customer</h6>
                                    <h3>{{ $customers->count() }}</h3>
                                </div>
                                <div class="db-icon">
                                    <img src="{{ URL::to('assets/img/icons/student-icon-01.svg') }}" alt="Dashboard Icon">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-lg-6">
                    <div class="card card-chart">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h5 class="card-title">Customers Overview</h5>
                                </div>
                                <div class="col-6">
                                    <ul class="chart-list-out">
                                        {{-- <li><span class="circle-blue"></span>Teacher</li> --}}
                                        <li><span class="circle-green"></span>Customers</li>
                                        <li class="star-menus"><a href="javascript:;"><i class="fas fa-ellipsis-v"></i></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chart-customer"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        var customer = {
            chart: {
                type: 'bar',
                height: 350
            },
            series: [{
                name: 'Customer',
                data: @json($nilaiCustomer) // [0, 0, 5, 10, ...] per bulan
            }, ],
            xaxis: {
                categories: @json($bulan) // ['January', 'February', ...]
            },
            colors: ['#0dcaf0', '#0d6efd'], // biru info & biru primary
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '60%',
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                position: 'top'
            }
        }

        var chartCustomer = new ApexCharts(document.querySelector("#chart-customer"), customer);
        chartSp3.render();
    </script>
@endsection
