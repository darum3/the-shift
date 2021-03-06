<?php

namespace App\Http\Controllers\GroupManage;

use App\Eloquents\InputedDate;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Eloquents\Group;

class DesiredController extends Controller
{
    public function list(string $start_date = null)
    {
        $groupId = session('group_id');
        $members = Group::findOrFail(session('group_id'))->users()->orderBy('users.name')->get();
        $this->authorize('view', $members->first()->pivot->pivotParent);

        $date = is_null($start_date) ? today() : Carbon::parse($start_date);
        $date = $date->startOfWeek(Carbon::SUNDAY)->toImmutable();
        $lists = InputedDate::whereGroupId($groupId)->range($date, $date->addWeeks(2)->subDay())->get()->groupBy('user_id');

        return view('g-mng.desired.list', compact('members', 'lists', 'date'));
    }
}
