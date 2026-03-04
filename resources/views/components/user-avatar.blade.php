@props(['name', 'userId'])

<div class="d-flex align-items-center">
    <div class="avatar-pill bg-blue-ragon-gradient shadow-sm">
        {{ strtoupper(substr($name ?? '?', 0, 1)) }}
    </div>
    <div class="ms-3">
        <div class="fw-bold text-dark mb-0">{{ $name }}</div>
        <div class="x-small text-muted">ID: #{{ str_pad($userId, 4, '0', STR_PAD_LEFT) }}</div>
    </div>
</div>