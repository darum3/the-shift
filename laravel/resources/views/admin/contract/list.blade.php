@extends('layouts.admin')

@section('page_title')
契約一覧
@endsection

@section('content')
<div class="container">
    @if(session('contract-input-success', false))
    <div class='alert alert-success' role='alert'>
        登録しました.
    </div>
    @endif
    <div class='d-flex justify-content-end'>
        <a class='btn btn-primary' href="{{route('admin.contract.input')}}">追加</a>
    </div>
    {{-- TODO 検索 --}}
    <table class="table table-bordered table-hover">
        <thead class='thead-dark'>
            <tr>
                <th scope='col' style='width: 20%'>会社名</th>
            </tr>
        </thead>
        <tbody class='table-striped'>
            @foreach($contract as $row)
            <tr>
                <td>{{$row->name}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
