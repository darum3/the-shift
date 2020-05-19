<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Eloquents\Group;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function groupSelect(Request $request)
    {
        $groupId = $request->input('group');

        $group = Group::find($groupId);
        $this->authorize('view', $group);

        session([
            'group_id' => $group->id,
            'group_name' => $group->name,
        ]);
        return redirect()->route('home');
    }
}
