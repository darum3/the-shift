@extends('layouts.manage')

@section('page_title')
職種追加-確認
@endsection

@section('content')
@include('shared.add.confirm', compact('action', 'fields', 'data'))
@endsection
