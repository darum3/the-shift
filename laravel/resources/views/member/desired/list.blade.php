@extends('layouts.member')

@section('page_title')
シフト提出【{{session('group_name')}}】
@endsection

@section('scripts')
{{--  <script type="text/javascript" src="{{asset('js/shift.js')}}" defer></script>  --}}
@endsection

@section('content')
<div class='card col-sm-6'>
    <div class='card-header'>
        <div class='row'>
            <div class='col-sm-2'>
                <a href="{{ route('member.desired', ['week' => $week-1]) }}" class='btn btn-link'>前週</a>
            </div>
            <div class='col-sm-8'>
                <h5 class='text-center align-bottom'>
                    シフト提出状況：{{$from->format('m/d')}}〜{{$to->format('m/d')}}
                </h5>
            </div>
            <div class='col-sm-2'>
                <a href="{{ route('member.desired', ['week' => $week+1]) }}" class='btn btn-link text-right'>次週</a>
            </div>
        </div>
    </div>

    <div class='card-body'>
        @if(session('member.desired.fix'))
        <div class='alert-info'>シフトを提出しました</div>
        @endif
        <table class='table table-sm table-striped table-border'>
            <thead class='table-success'>
                <tr>
                    <th scope='col'>&nbsp;</th>
                    <th scope='col'>日付</th>
                    <th scope='col'>提出済み</th>
                    <th scope='col'>希望時間</th>
                </tr>
            </thead>
            <tbody>
                @for($it = $from->toMutable(); $it <= $to; $it->addDay())
                @php
                    $dayData = $desired->get($it->toDateString()) ?? [];
                    $fixed = $inputed->contains($it->toDateString());
                    $count = $count ?? 0 + is_null(optional($dayData)->first()) ? 0 : 1;
                @endphp
                <tr @if($fixed) class='table-secondary' @endif>
                    <td>
                        @if(!$fixed)
                        <form method=POST action="{{ route('member.desired.fix') }}">
                            @csrf
                            <button name='date[]' value="{{ $it->toDateString() }}">提出</button>
                        </form>
                        @endif
                    </td>
                    <td>
                        @if($fixed)
                        <span>{{$it->format('m/d')}}</span>
                        @else
                        <a href="{{ route('member.desired.edit', ['date' => $it->toDateString()]) }}" class='btn btn-sm btn-link'>{{ $it->format('m/d') }}</a>
                        @endif
                        {!! datetime_html_weekday($it->toImmutable()) !!}
                    </td>
                    <td>@if($fixed) ● @else &nbsp; @endif</td>
                    <td>
                        @foreach ($dayData as $item)
                            {{substr($item->time_start, 0, 5)}}〜{{substr($item->time_end, 0, 5)}}
                        @endforeach
                    </td>
                </tr>
                @endfor
            </tbody>
            @if($count === 7)一括確定@endif
        </table>
        <div>
            「提出」を行わないと入力しても提出されません。不可の場合も空のまま提出が必要です。
        </div>
    </div>
</div>
@endsection
