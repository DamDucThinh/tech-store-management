@php($sizeClass = isset($size) ? 'avatar-'.$size : null)
@if ($profile?->avatarUrl())
    <img src="{{ $profile->avatarUrl() }}" alt="{{ $profile->name }}" @class(['avatar', $sizeClass])>
@else
    <span @class(['avatar', $sizeClass]) aria-hidden="true">{{ $profile?->initials() ?: '?' }}</span>
@endif
