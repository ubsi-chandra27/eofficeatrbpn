@php
    $notificationUser = auth()->user();
    $unreadCount = $notificationUser?->unreadNotifications()->count() ?? 0;
    $topNotifications = $notificationUser
        ? $notificationUser->notifications()->latest()->limit(5)->get()
        : collect();
@endphp

<div class="dropdown notification-dropdown">
    <button
        class="topbar-icon notification-toggle"
        type="button"
        data-bs-toggle="dropdown"
        aria-expanded="false"
        aria-label="Buka notifikasi">
        <i class="bi bi-bell-fill"></i>
        @if($unreadCount > 0)
            <span class="notification-count">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
        @endif
    </button>

    <div class="dropdown-menu dropdown-menu-end notification-menu">
        <div class="notification-header">
            <div>
                <strong>Notifikasi</strong>
                <small>{{ $unreadCount }} belum dibaca</small>
            </div>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit">Tandai semua</button>
                </form>
            @endif
        </div>

        <div class="notification-list">
            @forelse($topNotifications as $notification)
                @php
                    $data = $notification->data;
                    $type = $data['type'] ?? 'info';
                    $icon = $data['icon'] ?? 'bi-bell-fill';
                @endphp
                <a
                    href="{{ route('notifications.open', $notification->id) }}"
                    class="notification-item {{ is_null($notification->read_at) ? 'unread' : '' }}">
                    <span class="notification-icon {{ $type }}">
                        <i class="bi {{ $icon }}"></i>
                    </span>
                    <span class="notification-copy">
                        <strong>{{ $data['title'] ?? 'Notifikasi' }}</strong>
                        <small>{{ $data['message'] ?? 'Ada pembaruan baru.' }}</small>
                        <time>{{ $notification->created_at?->diffForHumans() }}</time>
                    </span>
                </a>
            @empty
                <div class="notification-empty">
                    <i class="bi bi-bell"></i>
                    <span>Belum ada notifikasi.</span>
                </div>
            @endforelse
        </div>
    </div>
</div>
