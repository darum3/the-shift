<form method='POST' action="{{ $action }}">
    @csrf
    @foreach($fields as $field)
    <div class="form-group row">
        <label for="{{$field['name']}}" class="col-sm-2 col-form-label">{{$field['label']}}</label>
        <div class="{{'col-sm-'.$field['width']}}">
            <input type="{{$field['type'] ?? 'text'}}" class="form-control @error($field['name']) is-invalid @enderror" id="{{$field['name']}}" name="{{$field['name']}}" value="{{old($field['name'])}}" />
            @error($field['name']) <span class='invalid-feedback' role='alert'><strong>{{$message}}</strong></span> @enderror
        </div>
    </div>
    @endforeach
    <div class="form-group row">
        <div class='col-sm-2'>
            <button type="submit" class='btn btn-primary'>確認</button>
            <button type="submit" class='btn btn-warning' name='back'>戻る</button>
        </div>
    </div>
</form>
