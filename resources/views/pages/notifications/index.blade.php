<x-layouts.app title="Notifikasi">
    <div class="mx-auto max-w-4xl space-y-6 px-4 py-8 sm:px-8 sm:py-10">
        <div>
            <h1 class="text-2xl font-bold text-text">Notifikasi</h1>
            <p class="mt-1 text-text-secondary">Pembaruan terkait laporan dan klaim Anda.</p>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-success-text bg-success-bg p-4 text-success-text" role="status">{{ session('success') }}</div>
        @endif

        <div class="space-y-3">
            @forelse ($notifications as $notification)
                <article class="flex flex-col gap-3 rounded-lg sm:flex-row sm:items-start sm:justify-between sm:gap-4 border border-border {{ $notification->read_at ? 'bg-surface-white' : 'bg-primary/5' }} p-4 sm:p-5">
                    <div class="min-w-0 break-words">
                        <h2 class="font-bold text-text">{{ $notification->title }}</h2>
                        <p class="mt-1 text-sm text-text-secondary">{{ $notification->message }}</p>
                        <time class="mt-2 block text-xs text-text-muted" datetime="{{ $notification->created_at->toIso8601String() }}">
                            {{ $notification->created_at->locale('id')->translatedFormat('d M Y, H:i') }}
                        </time>
                    </div>
                    @if (! $notification->read_at)
                        <form method="POST" action="{{ route('notifications.read', $notification) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="whitespace-nowrap text-sm font-semibold text-primary">Tandai dibaca</button>
                        </form>
                    @else
                        <span class="whitespace-nowrap text-xs text-text-muted">Sudah dibaca</span>
                    @endif
                </article>
            @empty
                <p class="rounded-lg border border-dashed border-border p-8 text-center text-text-secondary">Belum ada notifikasi.</p>
            @endforelse
        </div>

        {{ $notifications->links() }}
    </div>
</x-layouts.app>
