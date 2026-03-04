@props(['name', 'userId', 'subtitle' => null])

<div class="d-flex align-items-center">
    <div class="avatar-pill bg-blue-ragon-gradient shadow-sm" style="width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
        {{ strtoupper(substr($name ?? '?', 0, 1)) }}
    </div>
    <div class="ms-3">
        <div class="fw-bold text-main mb-0" style="font-size: 0.9rem;">{{ $name }}</div>
        <div class="x-small text-muted">
            {{ $subtitle ? strtoupper($subtitle) : 'ID: #' . str_pad($userId, 4, '0', STR_PAD_LEFT) }}
        </div>
    </div>
</div>