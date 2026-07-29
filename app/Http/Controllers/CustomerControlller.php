<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Service\CustomerService;
use Illuminate\Http\Request;

class CustomerControlller extends Controller
{
    public function index()
    {
        return view('apotik.index');
    }

    public function getDataCustomers(Request $request)
    {
        $response = CustomerService::getCustomerData($request);
        return response()->json($response)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');;
    }

    public function reload()
    {
        $response = CustomerService::reload();
        return response()->json($response);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:customers,id',
            'metode_bayar' => 'required|in:QRIS,TUNAI,TRANSFER',
            'uang_tunai' => 'required_if:metode_bayar,TUNAI|nullable|numeric|min:0',
            'nama_customer' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:20',
        ]);
        $response = CustomerService::updateCustomer($request);
        return response()->json($response);
    }

    public function cetak($id)
    {
        $customer = Customer::with('transaksiObat')->findOrFail($id);
        return view('apotik.struk.cetak', compact('customer'));
    }
}
