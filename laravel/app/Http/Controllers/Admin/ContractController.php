<?php

namespace App\Http\Controllers\Admin;

use App\Eloquents\Contract;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContractInputConfirm;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index()
    {
        $contract = Contract::allContract()->get();
        return view('admin.contract.list', compact('contract'));
    }

    public function input()
    {
        return view('admin.contract.input');
    }

    public function inputConfirm(ContractInputConfirm $request)
    {
        $request->session()->flash('data', $request->all());
        return view('admin.contract.confirm');
    }

    public function inputExec(ContractInputConfirm $request)
    {
        Contract::create([
            'name' => $request->name,
        ]);
        $request->session()->flash('contract-input-success');
        return redirect()->route('admin.contract');
    }
}
