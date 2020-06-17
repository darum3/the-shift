@extends('layouts.g-manage')

@section('page_title')
シフト表示【{{session('group_name')}}】：{{$date->toDateString()}}
@endsection

@section('scripts')
<script type="text/javascript" src="{{asset('js/shift.js')}}" defer></script>
@endsection

@section('content')
<div class='float-right'><a href="{{ route('member.shift') . "?year={$date->year}&month={$date->month}" }}" class='btn btn-link'>戻る</a></div>
<div id="shift">
    <easygantt-vue-ctrl
        rest-api="{{route('g-manage.shift.json.get')}}"
        user-list-api="{{ route('g-manage.user.json.list') }}"
        date="{{$date->toDateString()}}"
    ></easygantt-vue-ctrl>
</div>
@endsection
