<table class='table table-sm'>
    <tbody>
        @foreach($fields as $field)
        <tr>
            <th scope='col' style="width: 16%" class='table-success'>{{$field['label']}}</th>
            <td>{{$data[$field['name']]}}</td>
        </tr>
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
