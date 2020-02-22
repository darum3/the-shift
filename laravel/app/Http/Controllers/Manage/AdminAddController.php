<?php

namespace App\Http\Controllers\Manage;

use App\Eloquents\Group;
use App\Eloquents\UserGroup;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\AdminUserAdd;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAddController extends Controller
{
    public function input(int $groupId) {
        $group = Group::findOrFail($groupId);
        $this->authorize('view', $group);

        $fields = [
            ['label' => '名前', 'width' => 4, 'name' => 'user_name'],
            ['label' => 'メールアドレス', 'width' => 4, 'name' => 'email'],
            ['label' => 'パスワード', 'type' => 'password', 'width' => 4, 'name' => 'password'],
        ];
        $action = route('manage.group.admin_add.confirm', ['id' => $group->id]);
        return view('mng.group.admin_add.input', compact('group', 'fields', 'action'));
    }

    public function confirm(int $groupId, AdminUserAdd $request)
    {
        $group = Group::findOrFail($groupId);
        $this->authorize('view', $group);

        $data = $request->all();

        $fields = [
            ['label' => '名前', 'width' => 4, 'name' => 'user_name'],
            ['label' => 'メールアドレス', 'width' => 4, 'name' => 'email'],
            ['label' => 'パスワード', 'type' => 'password', 'width' => 4, 'name' => 'password'],
        ];

        $action = route('manage.group.admin_add.exec', ['id' => $group->id]);
        return view('mng.group.admin_add.confirm', compact('group', 'fields', 'action', 'data'));
    }

    public function exec(int $groupId, AdminUserAdd $request)
    {
        $group = Group::findOrFail($groupId);
        $this->authorize('view', $group);
        if ($request->has('back')) {
            return redirect()->redirect('manage.group.admin_add', ['id' => $group->id]);
        }

        DB::transaction(function () use($groupId, $request) {
            $user = User::create([
                'name' => $request->user_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            UserGroup::create([
                'group_id' => $groupId,
                'user_id' => $user->id,
                'flg_admin' => true,
            ]);
        });

        session()->flash('group.add.success');
        return redirect()->route('manage.group');
    }
}
