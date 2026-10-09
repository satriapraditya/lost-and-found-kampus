<x-layouts.admin title="Tambah Admin" subtitle="Buat akun baru yang langsung bisa masuk ke panel admin.">
    <x-slot:actions>
        <x-button variant="secondary" :href="route('admin.admins.index')">
            <x-admin.icon name="arrow-left" class="h-4 w-4" />
            Kembali
        </x-button>
    </x-slot:actions>

    <form method="POST" action="{{ route('admin.admins.store') }}" class="flex max-w-2xl flex-col gap-5 rounded-lg border border-border bg-surface-white p-5 shadow-card md:p-6">
        @csrf

        <x-form-field name="name" label="Nama Lengkap" :value="old('name')" :error="$errors->first('name')" required />

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <x-form-field name="nim" label="NIM / NIP" :value="old('nim')" :error="$errors->first('nim')" required />
            <x-form-field name="study_program" label="Unit / Program Studi" placeholder="Contoh: Staf Kemahasiswaan" :value="old('study_program')" :error="$errors->first('study_program')" required />
        </div>

        <x-form-field type="email" name="email" label="Email" :value="old('email')" :error="$errors->first('email')" required />

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <x-form-field type="password" name="password" label="Password" placeholder="Minimal 8 karakter" :error="$errors->first('password')" required autocomplete="new-password" />
            <x-form-field type="password" name="password_confirmation" label="Ulangi Password" required autocomplete="new-password" />
        </div>

        <div class="flex justify-end gap-2 border-t border-border pt-5">
            <x-button variant="secondary" :href="route('admin.admins.index')">Batal</x-button>
            <x-button type="submit" variant="primary">Simpan Admin</x-button>
        </div>
    </form>
</x-layouts.admin>
