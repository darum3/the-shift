<?php

namespace App\Http\Controllers\Admin;

use App\Eloquents\Contract;
use App\Eloquents\Group;
use App\Eloquents\UserGroup;
use App\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContractInputConfirm;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    public function index()
    {
        $contract = Contract::allContract()->get();
        return view('admin.contract.list', compact('contract'));
    }

    public function show(int $id)
    {
        $contract = Contract::findOrFail($id)->load('groups', 'groups.users');
        return view('admin.contract.show', compact('contract'));
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
        if ($request->has('back')) {
            return redirect()->route('admin.contract.input')->withInput();
        }

        DB::transaction(function () use($request) {
            // Add Contract
            $contract = Contract::create([
                'name' => $request->name,
            ]);

            $group = Group::create([
                'contract_id' => $contract->id,
                'name' => '管理グループ',
                'flg_admin' => true,
            ]);

            $user = User::whereEmail($request->email)->first();
            if (is_null($user)) {
                $user = User::create([
                    'name' => $request->user_name,
                    'email' => $request->email,
                    'password' => bcrypt('password'), // TODO 初期パスワード
                ]);
            }

            UserGroup::create([
                'group_id' => $group->id,
                'user_id' => $user->id,
                'flg_admin' => true,
            ]);
        });

        $request->session()->flash('contract-input-success');
        return redirect()->route('admin.contract');
    }
}
