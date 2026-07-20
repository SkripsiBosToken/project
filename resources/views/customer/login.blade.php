<x-layout.auth title="Masuk | Kusuka Catering">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 md:text-3xl">Selamat Datang Kembali</h1>
        <p class="mt-2 text-sm text-gray-500">Masuk untuk melanjutkan pemesanan Anda.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ showPassword: false }">
        @csrf

        <x-ui.input name="username" label="Username" icon="fa-user" placeholder="Masukkan username" required
            autocomplete="username" autofocus />

        <div>
            <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">
                Password <span class="text-primary-danger">*</span>
            </label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="fa-solid fa-lock text-sm"></i>
                </span>
                <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required
                    autocomplete="current-password" placeholder="Masukkan password"
                    class="w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-10 text-sm transition placeholder:text-gray-400 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                {{-- Toggle visibilitas password mengurangi kesalahan ketik. --}}
                <button type="button" @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 transition hover:text-primary"
                    :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'">
                    <i class="fa-solid text-sm" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('forgot-password') }}" class="text-sm font-medium text-primary hover:underline">
                Lupa password?
            </a>
        </div>

        <x-ui.button type="submit" size="lg" block icon="fa-right-to-bracket">Masuk</x-ui.button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-500">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-semibold text-primary hover:underline">Daftar sekarang</a>
    </p>

</x-layout.auth>
