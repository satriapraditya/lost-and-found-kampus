@php
    $fieldClass = 'h-11 w-full rounded-sm border border-border bg-surface-white px-3 text-small text-text focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20';
@endphp

<x-layouts.admin title="Daftar User" subtitle="Mahasiswa dan civitas yang terdaftar di Campus Find.">
    <section class="flex flex-col gap-5 rounded-lg border border-border bg-surface-white p-5 shadow-card md:p-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col gap-3 sm:flex-row">
            <input
                type="search"
                name="q"
                value="{{ $search }}"
                placeholder="Cari nama, NIM, atau email…"
                aria-label="Cari user"
                class="{{ $fieldClass }} sm:flex-1"
            >
            <x-button type="submit" variant="primary">Cari</x-button>
        </form>

        @if ($users->isEmpty())
            <div class="flex items-center justify-center rounded-md border border-dashed border-border py-12 text-small text-text-muted">
                {{ $search ? 'Tidak ada user yang cocok.' : 'Belum ada user terdaftar.' }}
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-small">
                    <thead>
                        <tr class="border-b border-border text-caption uppercase tracking-wide text-text-muted">
                            <th class="py-2.5 pr-4 font-semibold">Nama</th>
                            <th class="py-2.5 pr-4 font-semibold">Email</th>
                            <th class="py-2.5 pr-4 text-center font-semibold">Laporan</th>
                            <th class="py-2.5 pr-4 text-center font-semibold">Klaim</th>
                            <th class="py-2.5 pr-4 font-semibold">Daftar</th>
                            <th class="py-2.5 font-semibold"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr class="border-b border-surface-muted last:border-0">
                                <td class="py-3 pr-4">
                                    <p class="font-semibold text-text">{{ $user->name }}</p>
                                    <p class="text-caption text-text-muted">{{ $user->nim }} · {{ $user->study_program }}</p>
                                </td>
                                <td class="py-3 pr-4 text-text-secondary">{{ $user->email }}</td>
                                <td class="py-3 pr-4 text-center text-text">{{ $user->reports_count }}</td>
                                <td class="py-3 pr-4 text-center text-text">{{ $user->claims_count }}</td>
                                <td class="py-3 pr-4 text-text-secondary">{{ $user->created_at?->locale('id')->translatedFormat('d M Y') ?? '—' }}</td>
                                <td class="py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <form
                                            method="POST"
                                            action="{{ route('admin.admins.promote', $user) }}"
                                            onsubmit="return confirm('Jadikan akun ini admin? Dia akan bisa membuka panel admin.')"
                                        >
                                            @csrf
                                            @method('PATCH')
                                            <x-button type="submit" variant="secondary" class="!px-3 !py-1.5">Jadikan Admin</x-button>
                                        </form>
                                        <form
                                            method="POST"
                                            action="{{ route('admin.users.destroy', $user) }}"
                                            onsubmit="return confirm('Hapus akun ini? Semua laporan dan klaimnya ikut terhapus permanen.')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <x-button type="submit" variant="danger" class="!px-3 !py-1.5" aria-label="Hapus akun {{ $user->name }}" title="Hapus akun">
                                                <x-admin.icon name="trash" class="h-4 w-4" />
                                            </x-button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-admin.pagination :paginator="$users" noun="user" />
        @endif
    </section>
</x-layouts.admin>
