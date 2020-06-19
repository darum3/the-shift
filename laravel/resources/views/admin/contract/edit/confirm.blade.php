@extends('layouts.admin')

@section('page_title')
契約編集ー確認
@endsection

@section('content')
@include('shared.edit.confirm', compact('action', 'fields', 'data'))
@endsection
