@extends('layouts.admin')

@section('page_title')
契約追加
@endsection

@section('content')
    <form method='POST' action="{{route('admin.contract.input.confirm')}}">
        @csrf
        <div class="form-group row">
            <label for="name" class="col-sm-2 col-form-label">契約名</label>
            <div class='col-sm-8'>
                <input type='text' class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{old('name')}}" />
                @error('name') <span class='invalid-feedback' role='alert'><strong>{{$message}}</strong></span> @enderror
            </div>
        </div>
        <div class="form-group row">
            <label for="email" class="col-sm-2 col-form-label">管理ユーザアドレス</label>
            <div class='col-sm-4'>
                <input type='text' class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{old('email')}}" />
                @error('email') <span class='invalid-feedback' role='alert'><strong>{{$message}}</strong></span> @enderror
            </div>
        </div>
        <div class="form-group row">
            <label for="user_name" class="col-sm-2 col-form-label">管理ユーザ名</label>
            <div class='col-sm-4'>
                <input type='text' class="form-control @error('user_name') is-invalid @enderror" id="user_name" name="user_name" value="{{old('user_name')}}" />
                @error('user_name') <span class='invalid-feedback' role='alert'><strong>{{$message}}</strong></span> @enderror
            </div>
        </div>
        <div class="form-group row">
            <div class='col-sm-2'>
                <button type="submit" class='btn btn-primary'>確認</button>
                <a href="{{ route('admin.contract') }}" class='btn btn-warning'>戻る</a>
            </div>
        </div>
    </form>
@endsection
