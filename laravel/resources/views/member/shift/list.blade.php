@extends('layouts.member')

@section('page_title')
シフト表示【{{session('group_name')}}】
@endsection

@section('scripts')
{{--  <script type="text/javascript" src="{{asset('js/shift.js')}}" defer></script>  --}}
@endsection

@section('content')
<div class="card col-sm-6">
    <div class="card-header">
        <div class="row">
            <div class='col-sm-2'>
                @if($carbon->format('Ym') > today()->format('Ym'))
                <a href="{{ route('member.shift').'?year='.$carbon->copy()->subMonth()->year.'&month='.$carbon->copy()->subMonth()->month }}" class='btn btn-link'>前月</a>
                @endif
            </div>
            <div class='col-sm-8'>
                <h5 class='text-center align-bottom'>{{$carbon->format('Y年m月')}}のシフト一覧</h5>
            </div>
            <div class='col-sm-2'><a href="{{ route('member.shift').'?year='.$carbon->copy()->addMonth()->year.'&month='.$carbon->copy()->addMonth()->month }}" class='btn btn-link text-right'>次月</a></div>
        </div>
    </div>

    <div class="card-body">
        <table class='table table-sm table-striped table-border'>
            <thead class='table-success'>
                <tr>
                    <th scope='col'>日付</th>
                    <th scope='col'>シフト</th>
                    <th scope='col'>開始時間</th>
                    <th scope='col'>終了時間</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shifts as $row)
                <tr>
                    @php $weekday = datetime_html_weekday($row->start_datetime->toImmutable()); @endphp
                    <td>
                        <a href="{{ route('member.shift.view').'?date='.$row->start_datetime->toDateString() }}">{{ $row->start_datetime->format('m/d') }}</a> {!! $weekday !!}</td>
                    <td>{{$row->work_type->name}}</td>
                    <td>{{$row->start_datetime->format('H:i')}}</td>
                    <td>{{$row->end_datetime->format('H:i')}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
