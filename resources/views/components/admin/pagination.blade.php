@props([
    'paginator',
    'noun' => 'data',
])

<div class="flex flex-col items-center justify-between gap-3 text-small text-text-secondary sm:flex-row">
    <p>Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} {{ $noun }}</p>
    @if ($paginator->hasPages())
        <div class="flex items-center gap-2">
            <x-button variant="secondary" :href="$paginator->previousPageUrl()" :disabled="$paginator->onFirstPage()">← Sebelumnya</x-button>
            <x-button variant="secondary" :href="$paginator->nextPageUrl()" :disabled="! $paginator->hasMorePages()">Berikutnya →</x-button>
        </div>
    @endif
</div>

{{--
    Contoh pakai:
    <x-admin.pagination :paginator="$reports" noun="laporan" />
--}}
