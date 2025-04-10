<nav class="w-full top-0 left-0 z-50 bg-transparent">
    <div class="flex justify-between h-16 items-center">
        <a href="{{ route('home') }}" class="text-primary text-xl md:text-3xl font-bold">{{ $setting->name }}</a>

        <button id="menu-btn" class="md:hidden text-primary focus:outline-none">
            ☰
        </button>

        <div id="menu"
            class="hidden md:flex md:items-center md:space-x-12 absolute md:static top-16 left-0 w-full md:w-auto bg-white md:bg-transparent shadow-md md:shadow-none p-4 md:p-0 rounded-md flex-col md:flex-row text-center">
            <a href="{{ route('home') }}"
                class="text-primary font-bold text-sm md:text-lg hover:text-white py-2 md:py-0 transition duration-300">Home</a>
            <a href="{{ route('about') }}"
                class="text-primary font-semibold text-sm md:text-lg hover:text-white py-2 md:py-0 transition duration-300">About
                us</a>
            <a href="{{ route('catalogue') }}"
                class="text-primary font-semibold text-sm md:text-lg hover:text-white py-2 md:py-0 transition duration-300">Catalogue</a>
            <a href="{{ route('contact') }}"
                class="text-primary font-semibold text-sm md:text-lg hover:text-white py-2 md:py-0 transition duration-300">Contact
                Us</a>

            @guest
                <x-button.custom class="px-6 py-2 font-semibold text-sm md:text-lg rounded-md hover:bg-opacity-80"
                    href="{{ route('login') }}">Login</x-button.custom>
            @else
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="text-primary font-semibold text-sm md:text-lg hover:text-white py-2 md:py-0 transition duration-300 focus:outline-none">
                        {{ Auth::user()->name }}
                        <svg class="inline-block h-4 w-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-xl z-10">
                        
                        <a href="{{route('cart')}}"
                            class="block px-4 py-2 text-sm text-primary-gray hover:bg-primary hover:text-white">
                            Cart
                        </a>
                        <a href="{{route('order-list')}}"
                            class="block px-4 py-2 text-sm text-primary-gray hover:bg-primary hover:text-white">
                            Order
                        </a>
                        <a href="{{route('profile')}}"
                            class="block px-4 py-2 text-sm text-primary-gray hover:bg-primary hover:text-white">
                            Profile
                        </a>
                        <a href="{{route('logout')}}"
                            class="block px-4 py-2 text-sm text-primary-gray hover:bg-primary-danger hover:text-white">
                            Log Out
                        </a>
                    </div>
                </div>
            @endguest
        </div>
    </div>
</nav>

<script>
    document.getElementById("menu-btn").addEventListener("click", function() {
        const menu = document.getElementById("menu");
        menu.classList.toggle("hidden");
        menu.classList.toggle("flex");
    });

    document.addEventListener("scroll", function() {
        const navbar = document.querySelector("nav");
        if (window.scrollY > 50) {
            navbar.classList.add("bg-gray-900", "bg-opacity-90");
        } else {
            navbar.classList.remove("bg-gray-900", "bg-opacity-90");
        }
    });
</script>