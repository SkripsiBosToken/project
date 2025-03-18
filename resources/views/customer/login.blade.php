<x-layout.auth>

    @if (session('error'))
        <div class="fixed top-8 left-8 px-4 py-4 bg-primary-danger text-white p-4 rounded-lg shadow-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col justify-center px-8 md:py-0">
        <div class="text-2xl font-bold mb-4 md:text-left text-center">
            Hello Again!
        </div>
        <div class="text-lg mb-8 md:text-left text-center">
            Welcome Back
        </div>
        <div class="w-full">
            <x-form.custom action="{{ route('login') }}" method="post">
                <div class="mb-4">
                    <input name="username" type="text" placeholder="Username" class="w-full p-2 border rounded"
                        required>
                </div>
                <div class="mb-4">
                    <input name="password" type="password" placeholder="Password" class="w-full p-2 border rounded"
                        required>
                </div>
                <div class="mt-8 grid grid-flow-col grid-rows-4 gap-2">
                    <x-button.custom
                        class="w-full text-md md:text-xl bg-primary text-white py-3 rounded hover:bg-white hover:text-primary text-center"
                        type="submit">
                        Login
                    </x-button.custom>
                    <x-button.custom
                        class="w-full text-md md:text-xl bg-primary text-white py-3 rounded hover:bg-white hover:text-primary text-center"
                        href="{{ route('register') }}">
                        Sign Up
                    </x-button.custom>
                </div>
            </x-form.custom>
            <div class="text-center">
                <a href="#" class="text-primary">Forgot Password</a>
            </div>
        </div>
    </div>
</x-layout.auth>
