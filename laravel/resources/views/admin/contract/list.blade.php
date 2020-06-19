@extends('layouts.admin')

@section('page_title')
契約一覧
@endsection

@section('content')
@if(session('contract-input-success', false))
<div class='alert alert-success' role='alert'>
    登録しました.
</div>
@endif
<div class='d-flex justify-content-end'>
    <a class='btn btn-primary' href="{{route('admin.contract.input')}}">追加</a>
</div>
{{-- TODO 検索 --}}
<table class="table table-bordered table-hover" style='width:800px;'>
    <thead class='thead-dark'>
        <tr>
            <th scope='col' style='width: 25%'>会社名</th>
            <th scope='col' style='width: 8%'>&nbsp;</th>
        </tr>
    </thead>
    <tbody class='table-striped'>
        @foreach($contract as $row)
        <tr>
            <td>{{$row->name}}</td>
            <td>
                <a href="{{ route('admin.contract.show', ['id' => $row->id]) }}" class='btn btn-sm btn-secondary'>詳細</a>
                <a href="{{ route('admin.contract.edit', ['contract_id' => $row->id]) }}" class='btn btn-sm btn-warning'>編集</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
