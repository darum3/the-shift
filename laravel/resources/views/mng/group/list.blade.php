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
    <div class='col-sm-6'>
        <div class='clearfix'>
            <a href="{{route('manage.group.add.input')}}" class='btn btn-primary btn-sm float-right'>追加</a>
        </div>
        <table class='table table-striped table-sm'>
            <thead class='table-success'>
                <tr>
                    <th scope='col'>グループ名</th>
                    <th scope='col'>管理ユーザ</th>
                    <th scope='col'>メールアドレス</th>
                    <th scope='col'>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groups as $group)
                @php
                $admins = $group->users->filter(function($item){
                    return $item->pivot->flg_admin;
                });
                @endphp
                <tr>
                    <td>{{$group->name}}</td>
                    <td>{{optional($admins->first())->name}}</td>
                    <td>{{optional($admins->first())->email}}</td>
                    <td>@if($admins->isEmpty())<a href="{{route('manage.group.admin_add.input', ['id' => $group->id])}}" class='btn btn-info btn-sm'>管理ユーザ追加</a>@endif</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
