<table class='table table-sm'>
    <tbody>
        @foreach($fields as $field)
        @php
            $type = $field['type'] ?? 'text';
        @endphp
        @if($type !== 'hidden')
        <tr>
            <th scope='col' style="width: 16%" class='table-success'>{{$field['label']}}</th>
            <td>
                @if($type === 'password')
                    ********
                @elseif($type === 'color')
                    <div style="border: 1px solid black; background-color:{{ $data[$field['name']] }};">
                        {{ $data[$field['name']] }}
                    </div>
                @else
                    {{$data[$field['name']]}}
                @endif
            </td>
        </tr>
        @endif
        @endforeach
    </tbody>
</table>
<form method=POST class='d-inline' action={{$action}}>
    @csrf
    @foreach ($fields as $field)
    <input type='hidden' name="{{$field['name']}}" value="{{$data[$field['name']]}}" />
    @endforeach

    <button type='submit' class='btn btn-primary'>登録</button>
    <button type='submit' class='btn btn-warning' name='back'>戻る</button>
</form>
