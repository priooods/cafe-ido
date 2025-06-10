<?php

namespace App\Http\Controllers;

use App\Models\MCategoryTab;
use App\Models\TProductTab;
use App\Models\TTransactionTab;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        session_start();
        $tableNo = decrypt($id);
        $checkout = null;
        if(!$tableNo) redirect('/');
        if(!isset($_SESSION['session_product'])) $_SESSION['session_product'] = $id;
        else {
            if($_SESSION['session_product'] != $id) $_SESSION['session_product'] = $id;
            $checkout = TTransactionTab::where('session_product', $_SESSION['session_product'])->whereNotNull('t_transaction_checkout_tabs_id')->get();
        }
        $product = MCategoryTab::where('m_status_tabs_id',8)->with('product' , function($a){
                $a->whereIn('m_status_tabs_id',[2,3]);
            })
            ->whereHas('product', function($a){
                $a->whereIn('m_status_tabs_id',[2,3]);
            })
            ->orderBy('sequence','asc')
            ->get();
        return view('pages.product', compact('product', 'tableNo', 'checkout'));
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
}
