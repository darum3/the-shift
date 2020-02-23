@extends('layouts.g-manage')

@section('page_title')
ユーザ一覧
@endsection

@section('content')
@section('content')
@if(session('user.add.success'))
<div class='alert alert-success'>
    追加しました
</div>
@endif
<div class='row'>
    <div class='col-sm-6'>
        <div class='clearfix mb-2'>
            <form method=GET action="{{route('g-manage.user')}}" class='form-inline float-left'>
                <input type='text' class='form-control form-control-sm' name='search' placeholder="検索ワード" value="{{$search}}"/>
                <button class='btn btn-sm btn-success'>検索</button>
            </form>
            <a href="{{route('g-manage.user.add.input')}}" class='btn btn-primary btn-sm float-right'>追加</a>
        </div>
        <table class='table table-striped table-sm'>
            <thead class='table-success'>
                <tr>
                    <th scope='col'>名前</th>
                    <th scope='col'>メールアドレス</th>
                    <th scope='col'>管理者</th>
                </tr>
            </thead>
            <tbody>
                @foreach($group->users as $user)
                <tr>
                    <td>{{$user->name}}</td>
                    <td>{{$user->email}}</td>
                    <td>@if($user->pivot->flg_admin)○@endif</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
