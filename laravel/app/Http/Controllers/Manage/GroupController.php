<?php

namespace App\Http\Controllers\Manage;

use App\Eloquents\Group;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\GroupAdd;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::whereContractId(session('contract_id'))->where('flg_admin', false)->get();
        return view('mng.group.list', compact('groups'));
    }

    public function input()
    {
        $fields = [
            ['label' => '名前', 'width' => 4, 'name' => 'name'],
        ];
        $action = route('manage.group.add.confirm');
        return view('mng.group.add.input', compact('fields', 'action'));
    }

    public function confirm(GroupAdd $request)
    {
        if ($request->has('back')) {
            return redirect()->route('manage.group');
        }

        $data = $request->all();
        $fields = [
            ['label' => '名前', 'width' => 4, 'name' => 'name'],
        ];
        $action = route('manage.group.add.exec');
        return view('mng.group.add.confirm', compact('data', 'fields', 'action'));
    }

    public function exec(GroupAdd $request)
    {
        if ($request->has('back')) {
            return redirect()->route('manage.group.add.input')->withInput();
        }
        Group::create([
            'contract_id' => session('contract_id'),
            'name' => $request->name,
        ]);
        session()->flash('group.add.success');
        return redirect()->route('manage.group');
    }
}
