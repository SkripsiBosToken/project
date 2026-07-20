<x-layout.auth title="Lupa Password | Kusuka Catering">

    <div class="mb-8">
        <a href="{{ route('login') }}"
            class="mb-6 inline-flex items-center gap-2 text-sm font-medium text-gray-500 transition hover:text-primary">
            <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke halaman masuk
        </a>

        <h1 class="text-2xl font-bold text-gray-900 md:text-3xl">Lupa Password</h1>
        <p class="mt-2 text-sm text-gray-500">
            Masukkan email akun Anda. Kami akan mengirim tautan untuk mengatur ulang password.
        </p>
    </div>

    <form method="POST" action="{{ route('forgot-password') }}" class="space-y-4"
        x-data="{ submitting: false }" @submit="submitting = true">
        @csrf

        <x-ui.input name="email" type="email" label="Email" icon="fa-envelope" placeholder="nama@email.com" required
            autocomplete="email" autofocus />

        {{-- Tombol biasa dipakai di sini karena state loading-nya reaktif
             (Alpine), sedangkan prop `loading` komponen dievaluasi di server. --}}
        <button type="submit" :disabled="submitting"
            class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-6 py-3 text-base font-semibold text-white transition-colors hover:bg-primary-900 disabled:cursor-not-allowed disabled:opacity-50">
            <i class="fa-solid" :class="submitting ? 'fa-circle-notch fa-spin' : 'fa-paper-plane'"></i>
            <span x-text="submitting ? 'Mengirim…' : 'Kirim Tautan Reset'"></span>
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-500">
        Ingat password Anda?
        <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline">Masuk</a>
    </p>

</x-layout.auth>
