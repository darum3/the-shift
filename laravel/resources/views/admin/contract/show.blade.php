@extends('layouts.admin')

@section('page_title')
契約詳細【{{$contract->name}}】
@endsection

@section('content')
<a href='{{route('admin.contract')}}' href='btn btn-link'>一覧へ</a>
<table class='table table-sm'>
    <tbody>
        <tr>
            <th scope='row'>契約名</th>
            <td>{{$contract->name}}</td>
        </tr>
        <tr>
            <th scope='row'>グループ数</th>
            <td>{{ $contract->groups->count() }}</td>
        </tr>
        <tr>
            <th scope='row'>ユーザ総数</th>
            <td>{{ $contract->groups->sum(function($group) {
                return count($group->users);
            }) }}</td>
        </tr>
        <tr>
            <th class='table-success' colspan="2" scope='col'>管理者情報</th>
        </tr>
        @foreach($contract->groups->filter(function($value, $key) { return $value->flg_admin; }) as $adm_group)
            @foreach($adm_group->users as $admin)
            <tr>
                <td>{{$admin->name}}</td>
                <td>{{$admin->email}}</td>
            </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
@endsection
