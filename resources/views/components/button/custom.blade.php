@props([
    'href' => '#',
    'type' => 'button', // Ubah default type menjadi button
    // Sebelumnya default-nya `hover:bg-white` dipasangkan dengan `text-white`,
    // sehingga setiap tombol jadi putih-di-atas-putih (tidak terbaca) saat
    // disorot. Hover sekarang menggelapkan warna merek.
    'defbutton' => 'px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-900 transition-colors duration-200 inline-block'
])

@if ($type === 'submit')
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $defbutton]) }}>
        {{ $slot->isNotEmpty() ? $slot : 'Button' }}
    </button>
@else
    <a href="{{ $href }}" type="{{ $type }}" {{ $attributes->merge(['class' => $defbutton]) }}>
        {{ $slot->isNotEmpty() ? $slot : 'Button' }}
    </a>
@endif