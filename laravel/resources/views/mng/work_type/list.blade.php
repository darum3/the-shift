@extends('layouts.manage')

@section('page_title')
職種一覧
@endsection

@section('content')
@if(session('work_type.add.success'))
<div class='alert alert-success'>
    追加しました
</div>
@endif
<div class='row'>
    <div class='col-sm-4'>
        <div class='clearfix'>
            <a href="{{route('manage.work_type.add.input')}}" class='btn btn-primary btn-sm float-right'>追加</a>
        </div>
        <table class='table table-striped table-sm'>
            <thead class='table-success'>
                <tr>
                    <th scope='col'>コード</th>
                    <th scope='col'>名前</th>
                </tr>
            </thead>
            <tbody>
                @foreach($work_types as $row)
                <tr>
                    <td>{{$row->code}}</td>
                    <td>{{$row->name}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
