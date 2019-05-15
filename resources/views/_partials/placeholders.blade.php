<div class="col-md-12 mb-2 p-0">
    <p>
        <strong>Available placeholders</strong>
    </p>

    @foreach($placeHolders as $nameSpace => $values)
        <p>
            <strong>{{ title_case(str_replace('_', '', $nameSpace)) }}</strong>
        </p>

        @foreach($values as $placeHolder)
            <label class="badge badge-inverse">
                {{ '{'.$placeHolder.'}' }}
            </label>
        @endforeach
    @endforeach
</div>
