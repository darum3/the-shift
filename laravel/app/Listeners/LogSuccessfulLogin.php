<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        logger(Auth::user()->groups);
        $contractIds = [];
        foreach(Auth::user()->groups as $group) {
            $contractIds[] = $group->contract_id;
        }
        $contractIds = array_unique($contractIds);
        if (count($contractIds) !== 1) {
            dd('TODO Multi Contract User');
        }
        session(['contract_id' => $contractIds[0]]);

        // 所属グループが1つの場合はグループIDをセットする
        if (Auth::user()->groups->count() == 1) {
            session(['group_id' => Auth::user()->groups->first()->id]);
        }
    }
}
