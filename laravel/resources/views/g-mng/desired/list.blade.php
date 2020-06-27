@extends('layouts.g-manage')

@section('page_title')
シフト提出状況【{{session('group_name')}}】：{{$date->toDateString()}}〜
@endsection

{{--  @section('scripts')  --}}
{{--  <script type="text/javascript" src="{{asset('js/shift.js')}}" defer></script>  --}}
{{--  @endsection  --}}

@section('content')
<table class='table table-bordered'>
    <thead>
        <tr class='table-success'>
            <th scope='col'>名前</th>
            @for($it = $date->toMutable(); $it <= $date->addWeeks(2)->subDay(); $it->addDay())
            <th scope='col' class="@if($it->weekday() === 0) desired__th--sunday @elseif($it->weekday()===6) desired__th--saturday @endif">{{$it->format('m/d')}}</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @foreach($members as $member)
        <tr>
            <th scope='row'>{{$member->name}}</th>
            @for($it = $date->toMutable(); $it <= $date->addWeeks(2)->subDay(); $it->addDay())
            <td>
                @if(!is_null(optional($lists->get($member->id))->firstWhere('date_target', $it->toDateString())))●
                @else&nbsp;
                @endif
            </td>
            @endfor
        </tr>
        @endforeach
</table>
@endsection
