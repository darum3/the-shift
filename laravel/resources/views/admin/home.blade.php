@extends('layouts.admin')

@section('page_title')
ホーム
@endsection

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">契約管理</div>
            <div class="card-body">
                <div><a href="{{route('admin.contract')}}" class='btn btn-link'>契約一覧</a></div>
            </div>
        </div>
    </div>
</div>
@endsection
