@extends('layouts.manage')

@section('page_title')
ユーザ追加
@endsection

@section('content')
@include('shared.add.input', compact('fields', 'action'))
@endsection
