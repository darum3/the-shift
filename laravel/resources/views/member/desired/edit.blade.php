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
    ></desired-input-whole>
</div>
@endsection
