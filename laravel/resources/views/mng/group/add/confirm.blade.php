@extends('layouts.manage')

@section('page_title')
グループ追加-確認
@endsection

@section('content')
@include('shared.add.confirm', compact('action', 'fields', 'data'))
@endsection
