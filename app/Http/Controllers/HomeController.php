<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Customer;
use App\Models\Sp3;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    /** home dashboard */
    public function index()
    {
        $customers = Customer::whereYear('tanggal_registrasi', date('Y'))->get();
        $bulan = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];

        $dataCostumers = $customers->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->tanggal_registrasi)->format('F');
        })->map->count();
        $nilaiCustomer = collect($bulan)->map(fn($b) => $dataCustomers[$b] ?? 0)->values();
        return view('dashboard.home', compact('bulan', 'customers', 'nilaiCustomer'));
    }
    /** profile user */
    public function userProfile()
    {
        return view('dashboard.profile');
    }

    /** teacher dashboard */
    public function teacherDashboardIndex()
    {
        return view('dashboard.teacher_dashboard');
    }

    /** student dashboard */
    public function studentDashboardIndex()
    {
        return view('dashboard.student_dashboard');
    }
}
