<x-layout.customer>
    <div class=" font-poppins">
        <div class="p-4">
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <h1 class="text-2xl font-bold mb-4">Selesaikan Pembayaran Dalam</h1>
                <div class="text-4xl font-semibold text-primary-danger mb-2">{{ $data['expiry_time'] }}</div>
                <p class="text-primary-gray mb-4">Status Pembayaran : <span>{{ Str::of($data['transaction_status'])->upper() }}</span></p>

                @if ($data['transaction_status'] !== 'expire')
                    <div class="flex items-center justify-center mb-6">
                        <div class="mr-4">
                            <h2 class="text-md font-semibold">{{ Str::of($data['va_numbers'][0]['bank'])->upper() }}
                                Virtual Account</h2>
                            <p class="text-md text-primary-gray mt-2 md:mt-4">Nomor Virtual Account</p>
                            <p class="font-semibold text-xl">{{ $data['va_numbers'][0]['va_number'] }}</p>
                            <p class="text-md text-primary-gray mt-2 md:mt-4">Total pembayaran</p>
                            <p class="font-semibold text-xl">{{ 'Rp ' . number_format($data['gross_amount'], 0, ',', '.'); }}</p>
                        </div>
                    </div>
                @endif

            </div>

            <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                <h2 class="text-xl font-semibold mb-4">Cara Pembayaran</h2>
                <ol class="list-decimal list-inside space-y-2">
                    <li>Masukkan Kartu Anda.</li>
                    <li>Pilih Bahasa.</li>
                    <li>Masukkan PIN ATM Anda.</li>
                    <li>Kemudian, pilih Menu Lainnya.</li>
                    <li>Pilih Transfer dan pilih Jenis rekening yang akan Anda gunakan (Contoh: 'Dari Rekening
                        Tabungan').</li>
                    <li>Pilih Virtual Account Billing. Masukkan nomor Virtual Account Anda (Contoh: {{ $data['va_numbers'][0]['va_number'] }}).
                    </li>
                    <li>Tagihan yang harus dibayarkan akan muncul pada layar konfirmasi.</li>
                    <li>Konfirmasi, apabila telah sesuai, lanjutkan transaksi.</li>
                    <li>Transaksi Anda telah selesai.</li>
                </ol>
            </div>
        </div>
    </div>
</x-layout.customer>
