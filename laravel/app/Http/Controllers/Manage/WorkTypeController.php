<?php

namespace App\Http\Controllers\Manage;

use App\Eloquents\WorkType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\WorkTypeAdd;
use Illuminate\Http\Request;

class WorkTypeController extends Controller
{
    public function index()
    {
        $work_types = WorkType::whereContractId(session('contract_id'))->get();
        return view('mng.work_type.list', compact('work_types'));
    }

    public function input()
    {
        $fields = [
            ['label' => 'コード', 'width' => 3, 'name' => 'code'],
            ['label' => '名称', 'width' => 6, 'name' => 'name'],
        ];
        $action = route('manage.work_type.add.confirm');
        return view('mng.work_type.add.input', compact('fields', 'action'));
    }

    public function confirm(WorkTypeAdd $request)
    {
        if ($request->has('back'))
        {
            return redirect()->route('manage.work_type');
        }
        $data = $request->all();
        $fields = [
            ['label' => 'コード', 'width' => 3, 'name' => 'code'],
            ['label' => '名称', 'width' => 6, 'name' => 'name'],
        ];
        $action = route('manage.work_type.add.exec');
        return view('mng.work_type.add.confirm', compact('data', 'fields', 'action'));
    }

    public function exec(WorkTypeAdd $request)
    {
        if ($request->has('back')) {
            return redirect()->route('manage.work_type.add.input')->withInput();
        }
        WorkType::create([
            'contract_id' => session('contract_id'),
            'code' => $request->code,
            'name' => $request->name,
        ]);
        session()->flash('work_type.add.success');
        return redirect()->route('manage.work_type');
    }
}
