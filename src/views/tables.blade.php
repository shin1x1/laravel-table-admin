
@foreach ($tables as $table)

<p><a href="{{ url("crud/{$table}") }}">{{ $table }}</a></p>

@endforeach
