@extends('layouts.manage')

@section('page_title')
職種一覧
@endsection

@section('content')
@if(session('work_type.add.success'))
<div class='alert alert-success'>
    追加しました
</div>
@elseif(session('work_type.edit.success'))
<div class='alert alert-success'>
    更新しました
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
                    <th scope='col'>&nbsp;</th>
                    <th scope='col'>コード</th>
                    <th scope='col'>名前</th>
                    <th scope='col'>表示色</th>
                </tr>
            </thead>
            <tbody>
                @foreach($work_types as $row)
                <tr>
                    <td><a href="{{ route('manage.work_type.edit.input', ['id' => $row->id]) }}" class='btn btn-sm btn-warning'>編集</a></td>
                    <td>{{$row->code}}</td>
                    <td>{{$row->name}}</td>
                    <td>
                        <div style="border: 1px solid black; background-color: {{$row->work_color}}">&nbsp;</div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
