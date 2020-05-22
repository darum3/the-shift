<?php

namespace App\Http\Controllers\GroupManage;

use App\Eloquents\Group;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserJsonController extends Controller
{
    public function list(Request $request)
    {
        $group = Group::findOrFail(session('group_id'))->load('users');

        $this->authorize('view', $group);

        return ['users' => $group->users];
    }
}
