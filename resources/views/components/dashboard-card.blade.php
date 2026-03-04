@props(['route', 'icon', 'title'])

<a href="{{ $route }}" class="dashboard-card">
    <i class="{{ $icon }} fa-2x"></i>
    <h3 class="fw-bold">{{ $title }}</h3>
</a>