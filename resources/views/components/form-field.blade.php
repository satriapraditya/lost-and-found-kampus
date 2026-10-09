@props([
    'type' => 'text', // text | textarea | select | date
    'name',
    'label',
    'placeholder' => '',
    'required' => false,
    'error' => null,
    'options' => [], // untuk type=select, array ['value' => 'Label']
    'value' => '',
])

@php
    $inputBase = 'w-full rounded-sm border bg-surface-white px-4 py-3 text-label text-text placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors';
    $borderClass = $error ? 'border-danger-text' : 'border-border';
@endphp

<div class="flex flex-col gap-1.5">
    <label for="{{ $name }}" class="text-label font-semibold text-text-secondary">
        {{ $label }}@if ($required) <span class="text-danger-text">*</span>@endif
    </label>

    @if ($type === 'textarea')
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            placeholder="{{ $placeholder }}"
            @if ($required) required @endif
            rows="4"
            {{ $attributes->merge(['class' => $inputBase . ' ' . $borderClass . ' resize-none']) }}
        >{{ $value }}</textarea>
    @elseif ($type === 'select')
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            @if ($required) required @endif
            {{ $attributes->merge(['class' => $inputBase . ' ' . $borderClass]) }}
        >
            <option value="" disabled {{ $value === '' ? 'selected' : '' }}>{{ $placeholder ?: 'Pilih ' . $label }}</option>
            @foreach ($options as $optValue => $optLabel)
                <option value="{{ $optValue }}" {{ (string) $value === (string) $optValue ? 'selected' : '' }}>{{ $optLabel }}</option>
            @endforeach
        </select>
    @else
        <input
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            placeholder="{{ $placeholder }}"
            value="{{ $value }}"
            @if ($required) required @endif
            {{ $attributes->merge(['class' => $inputBase . ' ' . $borderClass]) }}
        />
    @endif

    @if ($error)
        <p class="text-small text-danger-text">{{ $error }}</p>
    @endif
</div>

{{--
    Contoh pakai:
    <x-form-field type="text" name="nama_barang" label="Nama Barang" placeholder="Contoh: Kunci Motor Honda" required />
    <x-form-field type="textarea" name="deskripsi" label="Deskripsi Ciri-ciri" required />
    <x-form-field type="select" name="kategori" label="Kategori" :options="['elektronik' => 'Elektronik', 'dokumen' => 'Dokumen']" required />
    <x-form-field type="text" name="nama_barang" label="Nama Barang" :error="$errors->first('nama_barang')" />
--}}
