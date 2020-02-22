@extends('layouts.manage')

@section('page_title')
管理ユーザ追加
@endsection

@section('content')
@include('shared.add.input', compact('fields', 'action'))
@endsection
