<nav class="w-full top-0 left-0 z-50 bg-transparent">
    <div class="flex justify-between h-16 items-center">
        <a href="{{route('home')}}" class="text-primary text-xl md:text-3xl font-bold">{{$setting->name}}</a>

        <button id="menu-btn" class="md:hidden text-primary focus:outline-none">
            ☰
        </button>

        <div id="menu"
            class="hidden md:flex md:items-center md:space-x-12 absolute md:static top-16 left-0 w-full md:w-auto bg-white md:bg-transparent shadow-md md:shadow-none p-4 md:p-0 rounded-md flex-col md:flex-row text-center">
            <a href="{{route('home')}}" class="text-primary font-bold text-sm md:text-lg hover:text-white py-2 md:py-0 transition duration-300">Home</a>
            <a href="{{route('about')}}" class="text-primary font-semibold text-sm md:text-lg hover:text-white py-2 md:py-0 transition duration-300">About us</a>
            <a href="{{route('catalogue')}}" class="text-primary font-semibold text-sm md:text-lg hover:text-white py-2 md:py-0 transition duration-300">Catalogue</a>
            <a href="{{route('contact')}}" class="text-primary font-semibold text-sm md:text-lg hover:text-white py-2 md:py-0 transition duration-300">Contact Us</a>

            <x-button.custom class="px-6 py-2 font-semibold text-sm md:text-lg rounded-md hover:bg-opacity-80"
                href="login">Login</x-button.custom>
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
