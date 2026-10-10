
<footer class="w-full border-t border-border bg-surface-white">
    <div class="mx-auto flex max-w-[1440px] flex-col gap-6 px-4 pb-10 pt-10 sm:px-8 lg:px-20 lg:pt-12">

        <div class="flex flex-col gap-8 lg:flex-row lg:items-start lg:justify-between">

            <!-- Logo dan deskripsi -->
            <div class="flex w-full flex-col gap-3 lg:w-[360px]">
                <div class="flex items-center gap-2.5">
                    <div class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-sm">
                      <img
    src="{{ asset('images/logo_lostandfound.png') }}"
    alt="Logo Campus Find"
    class="h-full w-full object-contain"
>
                    </div>

                    <span class="text-[16px] font-extrabold text-text">
                        Campus Find
                    </span>
                </div>

                <p class="text-small leading-relaxed text-text-secondary">
                    Sistem Informasi Layanan Lost &amp; Found Terpadu Universitas Negeri Yogyakarta.
                    Membantu mahasiswa dan civitas akademika menemukan kembali barang-barang
                    berharga yang hilang di area kampus.
                </p>
            </div>

            <!-- Informasi footer -->
            <div class="grid grid-cols-2 gap-x-6 gap-y-8 text-small sm:flex sm:gap-16">

                <div class="flex flex-col gap-3">
                    <span class="font-bold uppercase text-text">Layanan</span>
                    <span class="text-text-secondary">Semua Temuan</span>
                    <span class="text-text-secondary">Laporkan Barang</span>
                    <span class="text-text-secondary">Panduan Klaim</span>
                </div>

                <div class="flex flex-col gap-3">
                    <span class="font-bold uppercase text-text">Kampus</span>
                    <span class="text-text-secondary">Fakultas Teknik</span>
                    <span class="text-text-secondary">Universitas Negeri Yogyakarta</span>
                </div>

                <div class="col-span-2 flex flex-col gap-3">
                    <span class="font-bold uppercase text-text">Kontak</span>
                    <span class="text-text-secondary">Admin Lost &amp; Found</span>
                    <span class="break-all text-text-secondary sm:break-normal">
                        helpdesk@campusfind.ac.id
                    </span>
                </div>

            </div>
        </div>

        <hr class="border-border">

        <!-- Copyright dan media sosial -->
        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">

            <p class="text-small text-text-muted">
                © {{ date('Y') }} Campus Find Universitas. Hak Cipta Dilindungi Undang-Undang.
            </p>

            <div class="flex gap-4 text-text-muted">
                <a
                    href="https://www.instagram.com/fbiyaaldn/"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Instagram Campus Find"
                    class="transition hover:text-primary"
                >
                    IG
                </a>

                <a
                    href="https://x.com/USERNAME_TWITTER"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Twitter Campus Find"
                    class="transition hover:text-primary"
                >
                    TW
                </a>

                <a
                    href="https://www.facebook.com/USERNAME_FACEBOOK/"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Facebook Campus Find"
                    class="transition hover:text-primary"
                >
                    FB
                </a>
            </div>

        </div>
    </div>
</footer>
