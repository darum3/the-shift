<?php

namespace App\Http\Controllers\GroupManage;

use App\Eloquents\Group;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserJsonController extends Controller
{
    public function list(Request $request)
    {
        $users = Group::findOrFail(session('group_id'))->users()->orderBy('users.name')->get();

        $this->authorize('view', $users->first()->pivot->pivotParent);

        return ['users' => $users];
    }
}
