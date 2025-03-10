@props([
    'action' => '#',
    'method' => 'post',
])

<form class="w-full h-full" action="{{ $action }}" method="{{ $method }}">
    @if ($action != 'GET' || $action != 'get')
        @csrf
    @endif
    {{ $slot }}
</form>
