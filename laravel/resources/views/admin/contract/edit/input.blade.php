@extends('layouts.admin')

@section('page_title')
契約編集
@endsection

@section('content')
@include('shared.edit.input', compact('action', 'fields', 'data'))
@endsection
