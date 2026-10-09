@php
    $fieldClass = 'h-11 w-full rounded-sm border border-border bg-surface-white px-3 text-small text-text focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20';
@endphp

<x-layouts.admin title="Kelola Admin" subtitle="Akun yang bisa masuk ke panel admin. User biasa bisa dijadikan admin dari halaman Daftar User.">
    <x-slot:actions>
        <x-button variant="primary" :href="route('admin.admins.create')">
            <x-admin.icon name="plus" class="h-4 w-4" />
            Tambah Admin
        </x-button>
    </x-slot:actions>

    <section class="flex flex-col gap-5 rounded-lg border border-border bg-surface-white p-5 shadow-card md:p-6">
        <form method="GET" action="{{ route('admin.admins.index') }}" class="flex flex-col gap-3 sm:flex-row">
            <input
                type="search"
                name="q"
                value="{{ $search }}"
                placeholder="Cari nama, NIM/NIP, atau email…"
                aria-label="Cari admin"
                class="{{ $fieldClass }} sm:flex-1"
            >
            <x-button type="submit" variant="primary">Cari</x-button>
        </form>

        @if ($admins->isEmpty())
            <div class="flex items-center justify-center rounded-md border border-dashed border-border py-12 text-small text-text-muted">
                {{ $search ? 'Tidak ada admin yang cocok.' : 'Belum ada admin.' }}
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-left text-small">
                    <thead>
                        <tr class="border-b border-border text-caption uppercase tracking-wide text-text-muted">
                            <th class="py-2.5 pr-4 font-semibold">Nama</th>
                            <th class="py-2.5 pr-4 font-semibold">Email</th>
                            <th class="py-2.5 pr-4 font-semibold">Bergabung</th>
                            <th class="py-2.5 font-semibold"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($admins as $admin)
                            <tr class="border-b border-surface-muted last:border-0">
                                <td class="py-3 pr-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary/10 text-small font-bold text-primary">
                                            {{ strtoupper(mb_substr($admin->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-text">
                                                {{ $admin->name }}
                                                @if (auth()->id() === $admin->id)
                                                    <span class="ml-1 rounded-full bg-primary/10 px-2 py-0.5 text-[11px] font-semibold text-primary">Kamu</span>
                                                @endif
                                            </p>
                                            <p class="text-caption text-text-muted">{{ $admin->nim }} · {{ $admin->study_program }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 pr-4 text-text-secondary">{{ $admin->email }}</td>
                                <td class="py-3 pr-4 text-text-secondary">{{ $admin->created_at?->locale('id')->translatedFormat('d M Y') ?? '—' }}</td>
                                <td class="py-3 text-right">
                                    @unless (auth()->id() === $admin->id)
                                        <form
                                            method="POST"
                                            action="{{ route('admin.admins.demote', $admin) }}"
                                            onsubmit="return confirm('Cabut akses admin akun ini? Akunnya tetap ada sebagai user biasa.')"
                                        >
                                            @csrf
                                            @method('PATCH')
                                            <x-button type="submit" variant="danger" class="!px-3 !py-1.5">Cabut Akses</x-button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-admin.pagination :paginator="$admins" noun="admin" />
        @endif
    </section>
</x-layouts.admin>
