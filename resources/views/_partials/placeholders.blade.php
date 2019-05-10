<div class="col-md-12 mb-2 p-0">
    <p>
        <strong>Available placeholders</strong>
    </p>

    @foreach($placeHolders as $index => $placeholder)
        <label class="badge badge-inverse">
            {{ '{'.$placeholder.'}' }}
        </label>
    @endforeach
</div>
