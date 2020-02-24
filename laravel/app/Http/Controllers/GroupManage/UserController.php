<?php

namespace App\Http\Controllers\GroupManage;

use App\Eloquents\Group;
use App\Eloquents\UserGroup;
use App\Http\Controllers\Controller;
use App\Http\Requests\GroupManage\UserAdd;
use App\Http\Requests\GroupManage\UserDel;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $group = Group::findOrFail(session('group_id'))->load('users');
        $group->users = optional($group->users)->filter(function($item, $key) {
            return is_null($item->pivot->deleted_at);
        });
        if (!is_null($search)) {
            $email = $group->users->first(function($item) use($search) {
                return $item->email === $search;
            });
            if (is_null($email)) {
                $group->users = $group->users->filter(function($item) use($search) {
                    return strpos($item->name, $search) !== false;
                });
            } else {
                $group->users = collect([$email]);
            }
        }
        $this->authorize('view', $group);

        return view('g-mng.user.list', compact('group', 'search'));
    }

    public function add()
    {
        $group = Group::findOrFail(session('group_id'));
        $this->authorize('view', $group);

        $fields = [
            ['label' => '名前', 'width' => 4, 'name' => 'user_name'],
            ['label' => 'メールアドレス', 'width' => 4, 'name' => 'email'],
            ['label' => 'パスワード', 'type' => 'password', 'width' => 4, 'name' => 'password'],
        ];
        $action = route('g-manage.user.add.confirm');
        return view('g-mng.user.add.input', compact('group', 'fields', 'action'));
    }

    public function confirm(UserAdd $request)
    {
        $group = Group::findOrFail(session('group_id'));
        $this->authorize('view', $group);

        // ユーザが存在しているかチェック
        $user = User::whereEmail($request->email)->first();
        if(!is_null($user)) {
            return view('g-mng.user.add.connect_confirm', compact('user'));
        }

        $data = $request->all();
        $fields = [
            ['label' => '名前', 'width' => 4, 'name' => 'user_name'],
            ['label' => 'メールアドレス', 'width' => 4, 'name' => 'email'],
            ['label' => 'パスワード', 'type' => 'password', 'width' => 4, 'name' => 'password'],
        ];
        $action = route('g-manage.user.add.exec');
        return view('g-mng.user.add.confirm', compact('data', 'group', 'fields', 'action'));
    }

    public function exec(UserAdd $request)
    {
        $group = Group::findOrFail(session('group_id'));
        $this->authorize('view', $group);
        if ($request->has('back')) {
            return redirect()->redirect('g-manage.user.add');
        }

        DB::transaction(function() use($group, $request) {
            $user = User::create([
                'name' => $request->user_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            UserGroup::create([
                'group_id' => $group->id,
                'user_id' => $user->id,
            ]);
        });

        session()->flash('user.add.success');
        return redirect()->route('g-manage.user');
    }

    public function connectExec(Request $request)
    {
        $group = Group::findOrFail(session('group_id'));
        $this->authorize('view', $group);
        if ($request->has('back')) {
            return redirect()->redirect('g-manage.user.add');
        }

        DB::transaction(function () use($group, $request) {
            UserGroup::create([
                'group_id' => $group->id,
                'user_id' => $request->user_id,
            ]);
        });

        session()->flash('user.add.success');
        return redirect()->route('g-manage.user');
    }

    public function delConfirm(int $user_id)
    {
        $entity = UserGroup::whereUserId($user_id)->whereGroupId(session('group_id'))->with('group', 'user')->first();
        $this->authorize('view', $entity->group->first());

        return view('g-mng.user.delete.confirm', compact('entity'));
    }

    public function delExec(int $user_id, UserDel $request)
    {
        if ($request->has('back')) {
            return redirect()->route('g-manage.user');
        }

        $entity = UserGroup::whereUserId($user_id)->whereGroupId(session('group_id'))->first();
        $this->authorize('delete', $entity);

        DB::transaction(function () use($user_id, $request) {
            UserGroup::whereUserId($user_id)->whereGroupId(session('group_id'))
            ->update([
                'deleted_at' => now(),
                'version' => intval($request->version) + 1,
            ]);
        });

        session()->flash('user.del.success');
        return redirect()->route('g-manage.user');
    }
}
