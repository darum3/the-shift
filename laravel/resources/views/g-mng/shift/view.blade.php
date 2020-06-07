@extends('layouts.g-manage')

@section('page_title')
シフト作成【{{session('group_name')}}】：{{$date->toDateString()}}
@endsection

@section('scripts')
<script type="text/javascript" src="{{asset('js/shift.js')}}" defer></script>
@endsection

@section('content')
<div id="shift">
    <easygantt-vue-ctrl
        rest-api="{{route('g-manage.shift.json.get')}}"
        user-list-api="{{ route('g-manage.user.json.list') }}"
        date="{{$date->toDateString()}}"
        edit
    ></easygantt-vue-ctrl>
</div>
@endsection
