<x-layout.customer>
    <div class="my-10 md:my-14">
        <div class="flex flex-col md:flex-row gap-6">
            <x-sidebar.customer />

            <main class="w-full md:w-3/4">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <x-card.product />
                    <x-card.product />
                    <x-card.product />
                    <x-card.product />

                    <x-card.product />
                    <x-card.product />
                    <x-card.product />
                    <x-card.product />
                </div>
            </main>
        </div>
    </div>
</x-layout.customer>
