<?php

namespace ZarulIzham\EMandate\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use ZarulIzham\EMandate\Models\Bank;
use ZarulIzham\EMandate\Messages\AuthorizationRequest;

class PaymentController extends Controller
{

    /**
     * Initiate the request authorization message to FPX
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request)
    {
        // dd($request->all());
        return view('e-mandate::redirect_to_bank', [
            'request' => (new AuthorizationRequest)->handle($request->all()),
        ]);
    }

    public function banks(Request $request)
    {
        $banks = Bank::query()->select('bank_id', 'name', 'short_name', 'status');

        if ($request->type) {
            $banks->types($request->type == '01' ? ['B2C'] : ['B2B']);
        }

        if ($request->name) {
            $banks->where('name', 'LIKE', "%$request->name%");
        }

        $banks = $banks->orderBy('short_name', 'ASC')->get();

        return response()->json([
            'banks' => $banks,
        ], 200);
    }
}
