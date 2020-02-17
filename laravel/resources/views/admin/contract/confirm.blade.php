@extends('layouts.admin')

@section('page_title')
契約追加-確認
@endsection

@section('content')
@empty(session('data'))
データエラー
@else
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<table class='table table-sm'>
    <tbody>
        <tr>
            <th scope='col' style="width: 16%">名前</th>
            <td>{{session('data')['name']}}</td>
        </tr>
        <tr>
            <th scope='col'>メールアドレス</th>
            <td>{{session('data')['email']}}</td>
        </tr>
        <tr>
            <th scope='col'>管理ユーザ名</th>
            <td>{{session('data')['user_name']}}</td>
        </tr>
    </tbody>
</table>
<form method='POST' class='d-inline' action="{{route('admin.contract.input.exec')}}">
    @csrf
    <input type='hidden' name='name' value='{{session('data')['name']}}' />
    <input type='hidden' name='email' value='{{session('data')['email']}}' />
    <input type='hidden' name='user_name' value='{{session('data')['user_name']}}' />

    <button type='submit' class='btn btn-primary'>登録</button>
    <button type='submit' class='btn btn-warning' name='back'>戻る</button>
</form>
@endif
@endsection
