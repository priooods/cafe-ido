<?php

namespace App\Http\Controllers;

use App\Models\MCategoryTab;
use App\Models\TTransactionCheckoutTab;
use App\Models\TTransactionTab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $tableNo = $request->table_number;
        return response()->json([
            'table_url' => env('APP_URL'). '/table/' . encrypt($tableNo) 
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tableNo = decrypt($id);
        $checkout = null;
        if (!$tableNo) redirect('/');
        $product = MCategoryTab::where('m_status_tabs_id',8)->with('product' , function($a){
                $a->whereIn('m_status_tabs_id',[2,3]);
            })
            ->whereHas('product', function($a){
                $a->whereIn('m_status_tabs_id',[2,3]);
            })
            ->orderBy('sequence','asc')
            ->get();
        return view('pages.product', compact('product', 'tableNo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function showDetail($tableNo, Request $request)
    {
        try {
            DB::beginTransaction();
            $cart = json_decode($request->cart, true);
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = false;
            $item_order = array();
            $gross_amount = 0;
            foreach (collect($cart) as $value) {
                $gross_amount += $value['price'] * $value['qty'];
                array_push($item_order, [
                    "id" => $value['id'],
                    "price" => $value['price'],
                    "quantity" => $value['qty'],
                    "name" => $value['name'],
                ]);
            }
            $orderId = 'ORDER-' . uniqid();
            $trasactionCheckout = TTransactionCheckoutTab::create([
                'order_id' => $orderId,
                'm_status_tabs_id' => 7,
                'customer_name' => $request->nama,
                'customer_phone' => $request->no_hp,
                'notes' => $request->notes,
                'cashier' => 1,
                'table_number' => $tableNo,
                'bill' => $gross_amount,
            ]);
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $gross_amount,
                ],
                'customer_details' => [
                    'first_name' => $request->nama,
                    'phone' => $request->no_hp,
                ],
                "item_details" => $item_order,
            ];
            foreach (collect($cart) as $key => $value) {
                TTransactionTab::create([
                    't_transaction_checkout_tabs_id' => $trasactionCheckout->id,
                    't_product_tabs_id' => $value['id'],
                    'count' => $value['qty'],
                ]);
            }
            DB::commit();
            $snapToken = \Midtrans\Snap::createTransaction($params)->redirect_url;
            return redirect($snapToken);
        } catch (\Throwable $th) {
            DB::rollBack();
            abort(400, $th->getMessage());
        }
    }

    public function callback(Request $request)
    {
        try {
            DB::beginTransaction();
            switch ($request->transaction_status) {
                case 'capture':
                case 'settlement':
                    $trasaction = TTransactionCheckoutTab::where('order_id', $request->order_id)->first();
                    if (isset($trasaction)) {
                        $trasaction->update([
                            'm_status_tabs_id' => 6,
                            'amount_paid' => $trasaction->amount_paid
                        ]);
                    }
                    DB::commit();
                    return view('pages.invoice_succes');
                    break;
                case 'deny':
                case 'cancel':
                case 'expire':
                case 'failure':
                    $trasaction = TTransactionCheckoutTab::where('order_id', $request->order_id)->first();
                    if (isset($trasaction)) {
                        $trasaction->update([
                            'm_status_tabs_id' => 8,
                            'amount_paid' => 0
                        ]);
                    }
                    DB::commit();
                    return view('pages.invoice_failure');
                    break;
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            abort(400, $th->getMessage());
        }
    }
}
