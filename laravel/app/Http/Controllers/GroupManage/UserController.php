<?php

namespace App\Http\Controllers\GroupManage;

use App\Eloquents\Group;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $group = Group::findOrFail(session('group_id'))->load('users');
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
}
