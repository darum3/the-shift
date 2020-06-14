@extends('layouts.manage')

@section('page_title')
職種編集
@endsection

@section('content')
@include('shared.edit.input', compact('data', 'fields', 'action'))
@endsection
