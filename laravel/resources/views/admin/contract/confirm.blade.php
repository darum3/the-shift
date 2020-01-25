@extends('layouts.admin')

@section('page_title')
契約追加
@endsection

@section('content')
@empty(session('data'))
データエラー
@else
<div class="container">
    <h4>契約追加-確認</h4>
    <table class='table table-sm'>
        <tbody>
            <tr>
                <th scope='col' style="width: 16%">名前</th>
                <td>{{session('data')['name']}}</td>
            </tr>
        </tbody>
    </table>
    <form method='POST' class='d-inline' action="{{route('admin.contract.input.exec')}}">
        @csrf
        <input type='hidden' name='name' value='{{session('data')['name']}}' />

        <button type='submit' class='btn btn-primary'>登録</button>
    </form>
    <form method='POST' class='d-inline' action="{{route('admin.contract.input.back')}}">
        <button type='submit' class='btn btn-warning'>戻る</button>
    </form>
</div>
@endif
@endsection
