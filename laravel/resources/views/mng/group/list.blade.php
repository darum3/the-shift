@extends('layouts.manage')

@section('page_title')
グループ一覧
@endsection

@section('content')
@if(session('group.add.success'))
<div class='alert alert-success'>
    追加しました
</div>
@endif
<div class='row'>
    <div class='col-sm-4'>
        <div class='clearfix'>
            <a href="{{route('manage.group.add.input')}}" class='btn btn-primary btn-sm float-right'>追加</a>
        </div>
        <table class='table table-striped table-sm'>
            <thead class='table-success'>
                <tr>
                    <th scope='col'>グループ名</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groups as $group)
                <tr>
                    <td>{{$group->name}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
