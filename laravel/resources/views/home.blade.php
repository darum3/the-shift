@extends('layouts.app')

@section('page_title')
ホーム
@endsection

@section('scripts')
{{--  <script type="text/javascript" src="easygantt/easygantt.js"></script>  --}}
{{--  <script type="text/javascript" src="easygantt/tasks.js"></script>  --}}

<script type="text/javascript" src="{{asset('js/shift.js')}}" defer></script>
@endsection

@section('content')
<div id="shift" class="row justify-content-center">
    <div class="col-md-12">
        @if(!Auth::user()->flg_system_admin)
        <form class='form-inline mb-3' method=POST action="{{route('group_select')}}">
            @csrf
            <label for='group'>グループ選択</label>
            <select class="custom-select custom-select-sm" name="group">
                @foreach(Auth::user()->groups as $group)
                <option @if($group->id === session('group_id')) selected @endif value="{{$group->id}}">{{$group->name}}</option>
                @endforeach
            </select>
            <button class='btn btn-sm btn-info'>切替</button>
        </form>
        @endif

        <div class="card">
            <div class="card-header">Dashboard</div>

            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                You are logged in!

            </div>
        </div>
    </div>
</div>
@endsection
