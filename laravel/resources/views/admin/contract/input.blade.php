@extends('layouts.admin')

@section('page_title')
契約追加
@endsection

@section('content')
<div class="container">
    <h4>契約追加</h4>
    <form method='POST' action="{{route('admin.contract.input.confirm')}}">
        @csrf
        <div class="form-group row">
            <label for="name" class="col-sm-2 col-form-label">契約名</label>
            <div class='col-sm-8'>
                <input type='name' class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{old('name')}}" />
                @error('name') <span ckass='invalid-feedback' role='alert'><strong>{{$message}}</strong></span> @enderror
            </div>
        </div>
        <div class="form-group row">
            <div class='col-sm-2'>
                <button type="submit" class='btn btn-primary'>確認</button>
            </div>
        </div>
    </form>
</div>
@endsection
