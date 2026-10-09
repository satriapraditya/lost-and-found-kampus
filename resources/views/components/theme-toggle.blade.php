{{-- Tombol ganti mode gelap/terang. Logika kliknya ada di resources/js/theme.js --}}
<button
    type="button"
    data-theme-toggle
    {{ $attributes->merge(['class' => 'flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-text-secondary transition-colors hover:bg-surface-muted hover:text-text']) }}
    aria-label="Ganti mode gelap/terang"
    title="Ganti mode gelap/terang"
>
    {{-- Yang tampil adalah tujuan: bulan saat terang, matahari saat gelap --}}
    <x-admin.icon name="moon" class="h-5 w-5 shrink-0 dark:hidden" />
    <x-admin.icon name="sun" class="hidden h-5 w-5 shrink-0 dark:block" />
</button>
