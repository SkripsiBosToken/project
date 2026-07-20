<x-layout.auth title="Atur Ulang Password | Kusuka Catering">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 md:text-3xl">Atur Ulang Password</h1>
        <p class="mt-2 text-sm text-gray-500">Masukkan password baru untuk akun Anda.</p>
    </div>

    <form method="POST" action="{{ route('reset-password', ['token' => $data['token']]) }}" class="space-y-4"
        x-data="resetPassword()" @submit="if (!canSubmit) $event.preventDefault()">
        @csrf

        <div>
            <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">
                Password Baru <span class="text-primary-danger">*</span>
            </label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="fa-solid fa-lock text-sm"></i>
                </span>
                <input :type="show ? 'text' : 'password'" name="password" id="password" required x-model="password"
                    autocomplete="new-password" placeholder="Minimal 8 karakter"
                    class="w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-10 text-sm transition placeholder:text-gray-400 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                <button type="button" @click="show = !show"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 transition hover:text-primary"
                    :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'">
                    <i class="fa-solid text-sm" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>

            <div x-show="password.length > 0" x-cloak class="mt-2">
                <div class="flex gap-1">
                    <template x-for="level in 3" :key="level">
                        <div class="h-1 flex-1 rounded-full transition-colors"
                            :class="strength >= level ? strengthColour : 'bg-gray-200'"></div>
                    </template>
                </div>
                <p class="mt-1 text-xs" :class="strengthTextColour" x-text="strengthLabel"></p>
            </div>
        </div>

        {{-- Konfirmasi password: sebelumnya tidak ada, sehingga satu salah
             ketik langsung mengunci pengguna dari akunnya sendiri. --}}
        <div>
            <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700">
                Ulangi Password Baru <span class="text-primary-danger">*</span>
            </label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="fa-solid fa-lock text-sm"></i>
                </span>
                <input :type="show ? 'text' : 'password'" name="password_confirmation" id="password_confirmation"
                    required x-model="confirmation" autocomplete="new-password" placeholder="Ketik ulang password"
                    class="w-full rounded-lg border py-2.5 pl-10 pr-3 text-sm transition placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30"
                    :class="mismatch ? 'border-primary-danger' : 'border-gray-300 focus:border-primary'">
            </div>

            <p x-show="mismatch" x-cloak class="mt-1.5 flex items-center gap-1.5 text-xs text-primary-danger">
                <i class="fa-solid fa-circle-exclamation"></i>Password tidak sama.
            </p>
        </div>

        <button type="submit" :disabled="!canSubmit"
            class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-6 py-3 text-base font-semibold text-white transition-colors hover:bg-primary-900 disabled:cursor-not-allowed disabled:opacity-50">
            <i class="fa-solid fa-check"></i> Simpan Password Baru
        </button>
    </form>

    @push('scripts')
        <script>
            function resetPassword() {
                return {
                    password: '',
                    confirmation: '',
                    show: false,

                    get mismatch() {
                        return this.confirmation.length > 0 && this.password !== this.confirmation;
                    },

                    get canSubmit() {
                        return this.password.length >= 8 && this.password === this.confirmation;
                    },

                    get strength() {
                        let score = 0;
                        if (this.password.length >= 8) score++;
                        if (/[A-Z]/.test(this.password) && /[a-z]/.test(this.password)) score++;
                        if (/\d/.test(this.password) || /[^A-Za-z0-9]/.test(this.password)) score++;
                        return score;
                    },

                    get strengthLabel() {
                        return ['Terlalu lemah', 'Lemah', 'Cukup', 'Kuat'][this.strength];
                    },

                    get strengthColour() {
                        return ['bg-red-400', 'bg-red-400', 'bg-amber-400', 'bg-green-500'][this.strength];
                    },

                    get strengthTextColour() {
                        return ['text-red-500', 'text-red-500', 'text-amber-600', 'text-green-600'][this.strength];
                    },
                };
            }
        </script>
    @endpush

</x-layout.auth>
