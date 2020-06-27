@extends('layouts.member')

@section('page_title')
希望シフト入力【{{session('group_name')}}】
@endsection

@section('scripts')
<script type="text/javascript" src="{{asset('js/desired_input.js')}}" defer></script>
@endsection

@section('content')
<div id="desired_input">
    <desired-input-whole
        work-types-json="{{json_encode($workTypes)}}"
        original-data-json="{{json_encode($originalDatas)}}"
        target="{{$target->toDateString()}}"
        sunday="{{$sunday->toDateString()}}"
        url="{{ route('member.desired.register') }}"
        list-url="{{ route('member.desired') }}"
    ></desired-input-whole>
</div>
@endsection
