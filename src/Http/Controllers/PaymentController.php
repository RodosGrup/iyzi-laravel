<?php

namespace RodosGrup\IyziLaravel\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function return(Request $request)
    {
        return redirect()->route('iyzico.laravel.gateway')
            ->with([
                'content' => $request->all()
            ]);
    }
}
