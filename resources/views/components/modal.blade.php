@props(['name', 'show' => false, 'maxWidth' => ''])

@php
$maxWidthClass = match ($maxWidth) {
    'sm' => 'modal-sm',
    'lg' => 'modal-lg',
    'xl' => 'modal-xl',
    default => '',
};
@endphp

<div class="modal fade {{ $show ? 'show d-block' : '' }}" id="modal-{{ $name }}" tabindex="-1" aria-hidden="true" @if($show) style="background: rgba(0,0,0,0.5);" @endif>
    <div class="modal-dialog {{ $maxWidthClass }}">
        <div class="modal-content">
            {{ $slot }}
        </div>
    </div>
</div>
