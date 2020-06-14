<?php

namespace App\Http\Controllers\Manage;

use App\Eloquents\Group;
use App\Eloquents\WorkType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\WorkTypeAdd;
use App\Http\Requests\WorkTypeEdit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function edit(int $id)
    {
        $group = Group::find(session('group_id'));
        $this->authorize('update', $group);
        $data = WorkType::findOrFail($id);

        $fields = [
            ['label' => 'コード', 'width' => 3, 'name' => 'code'],
            ['label' => '名称', 'width' => 6, 'name' => 'name'],
            ['label' => '表示色', 'width' => 2, 'name' => 'work_color', 'type' => 'color'],
            ['label' => 'Version', 'width' => 0, 'name' => 'version', 'type' => 'hidden'],
        ];
        $action = route('manage.work_type.edit.confirm', compact('id'));
        return view('mng.work_type.edit.input', compact('data', 'fields', 'action'));
    }

    public function editConfirm($id, WorkTypeEdit $request)
    {
        if($request->has('back')) {
            return redirect()->route('manage.work_type');
        }
        $group = Group::find(session('group_id'));
        $this->authorize('update', $group);

        WorkType::findOrFail($id);

        $data = $request->all();
        $fields = [
            ['label' => 'コード', 'width' => 3, 'name' => 'code'],
            ['label' => '名称', 'width' => 6, 'name' => 'name'],
            ['label' => '表示色', 'width' => 2, 'name' => 'work_color', 'type' => 'color'],
            ['label' => 'Version', 'width' => 0, 'name' => 'version', 'type' => 'hidden'],
        ];
        $action = route('manage.work_type.edit.exec', compact('id'));
        return view('mng.work_type.edit.confirm', compact('data', 'fields', 'action'));
    }

    public function editExec($id, WorkTypeEdit $request)
    {
        if ($request->has('back')) {
            return redirect()->route('manage.work_type.edit.input', compact('id'))->withInput();
        }

        $group = Group::find(session('group_id'));
        $this->authorize('update', $group);

        $entity = WorkType::findOrFail($id);
        $entity->code = $request->code;
        $entity->name = $request->name;
        $entity->work_color = $request->work_color;
        $entity->version = $request->version;
        try {
            DB::transaction(function () use($entity) {
                $entity->save();
            });
        } catch(Exception $e) {
            // TODO 排他制御
            throw $e;
        }

        session()->flash('work_type.edit.success');
        return redirect()->route('manage.work_type');
    }
}
