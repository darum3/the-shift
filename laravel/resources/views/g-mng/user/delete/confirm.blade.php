@extends('layouts.manage')

@section('page_title')
ユーザ削除-確認
@endsection

@section('content')
{{json_encode($errors->all(), JSON_UNESCAPED_UNICODE)}}
<div class='row'>
    <div class='col-sm-4'>
        <table class='table table-sm'>
            <tbody>
                <tr>
                    <th class='table-success' scope='col'>名前</th>
                    <td>{{$entity->user->name}}</td>
                </tr>
                <tr>
                    <th class='table-success' scope='col'>メールアドレス</th>
                    <td>{{$entity->user->email}}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<form class='form-inline' method=POST action="{{ route('g-manage.user.del.exec', ['user_id' => $entity->user_id]) }}">
    @csrf
    <input type='hidden' name='version' value="{{$entity->version}}" />
    <button class='btn btn-sm btn-primary mr-2'>実行</button>
    <button class='btn btn-sm btn-warning' name='back' value=1>戻る</button>
</form>
@endsection
