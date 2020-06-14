@extends('layouts.manage')

@section('page_title')
職種編集-確認
@endsection

@section('content')
@include('shared.edit.confirm', compact('action', 'fields', 'data'))
@endsection
