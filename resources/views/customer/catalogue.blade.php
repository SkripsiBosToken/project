<x-layout.customer>
    <div class="my-10 md:my-14" x-data="{
        products: {{ $products }},
        categories: {{ $categories }},
        activeCategory: null,
        searchQuery: '',
        getPrices(product) {
            if (!product || !product.product_variants || product.product_variants.length === 0) {
                return 'Harga Tidak Tersedia';
            }
            const prices = Array.from(product.product_variants).map(variant => variant.price);
            const minPrice = Math.min(...prices);
            const maxPrice = Math.max(...prices);
            let formattedPrice = '';
            if (minPrice === maxPrice) {
                formattedPrice = 'Rp' + new Intl.NumberFormat('id-ID').format(minPrice);
            } else {
                formattedPrice = 'Rp' + new Intl.NumberFormat('id-ID').format(minPrice) + ' - Rp' + new Intl.NumberFormat('id-ID').format(maxPrice);
            }
            return formattedPrice;
        },
        filterProducts(product) {
            const categoryMatch = this.activeCategory === null || product.category.name === this.activeCategory;
            const searchMatch = product.name.toLowerCase().includes(this.searchQuery.toLowerCase());
            return categoryMatch && searchMatch;
        },
        toggleCategory(categoryName) {
            this.currentPage = 1;
            if (this.activeCategory === categoryName) {
                this.activeCategory = null; // Deactivate if already active
            } else {
                this.activeCategory = categoryName; // Activate if not active
            }
        },
    
        currentPage: 1,
        itemsPerPage: 16,
        get paginatedProducts() {
            return this.products
                .filter(product => product.product_variants.length !== 0 && this.filterProducts(product))
                .slice((this.currentPage - 1) * this.itemsPerPage, this.currentPage * this.itemsPerPage);
        },
        get totalPages() {
            return Math.ceil(
                this.products.filter(product => product.product_variants.length !== 0 && this.filterProducts(product)).length / this.itemsPerPage
            );
        },
        goToPage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
            }
        }
    
    }">
        <div class="flex flex-col md:flex-row gap-6">
            {{-- Sidebar --}}
            <aside class="w-full md:w-1/4 rounded-lg font-poppins">
                <h2 class="text-lg md:text-2xl font-bold text-primary">Products</h2>
                <div class="mt-4">
                    <input type="text" placeholder="Search"
                        class="w-full p-2 border rounded-lg focus:ring focus:ring-primary" x-model="searchQuery" x-effect="currentPage = 1">
                </div>
                <h3 class="mt-6 text-sm md:text-lg font-semibold text-primary-gray">Jenis</h3>

                <div class="mt-2 flex flex-col gap-2 md:gap-8">
                    <template x-for="category in categories">
                        <button class="flex items-center gap-2 px-2 py-2 md:py-3 rounded-lg md:rounded-2xl text-primary"
                            x-bind:class="activeCategory === category.name ? 'border border-primary font-bold text-lg' :
                                'text-primary-gray'"
                            @click="toggleCategory(category.name)">
                            <span>🍽️
                                <span x-text="category.name"></span>
                            </span>
                        </button>
                    </template>
                </div>
            </aside>
            <main class="w-full md:w-3/4">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <template x-for="product in paginatedProducts">
                        <template x-if="product.product_variants.length != 0 && filterProducts(product)">
                            <div
                                class="border border-primary rounded-xl md:rounded-2xl p-2 md:p-4 shadow-md h-full font-poppins">
                                <h3 class="text-sm md:text-xl font-semibold text-primary text-center"
                                    x-text="product.name">
                                </h3>
                                <div class="flex justify-center py-4">
                                    <img x-bind:src="JSON.parse(product.product_variants[0].photo)[0]"
                                        class="w-20 md:w-36 h-20 md:h-36 object-cover rounded-md">
                                </div>
                                <hr class="border-primary py-2">

                                <p class="text-xs md:text-sm text-primary-gray line-clamp-2"
                                    x-text="product.product_variants[0].description">
                                </p>
                                <div class="flex justify-between items-center mt-4">
                                    <p class="text-xs md:text-sm font-bold text-primary" x-text="getPrices(product)">
                                    </p>
                                    <x-button.custom class="p-0.5 text-sm md:text-sm"
                                        x-bind:href="'/catalogue-detail/' + product.id">X</x-button.custom>
                                </div>
                            </div>
                        </template>
                    </template>
                </div>
                <div class="flex justify-center mt-6 space-x-2">
                    <button class="px-3 py-1 border rounded" :disabled="currentPage === 1"
                        @click="goToPage(currentPage - 1)">
                        &laquo;
                    </button>

                    <template x-for="page in totalPages">
                        <button class="px-3 py-1 border rounded"
                            :class="{ 'bg-primary text-white': page === currentPage }" @click="goToPage(page)"
                            x-text="page">
                        </button>
                    </template>

                    <button class="px-3 py-1 border rounded" :disabled="currentPage === totalPages"
                        @click="goToPage(currentPage + 1)">
                        &raquo;
                    </button>
                </div>

            </main>
        </div>
    </div>
</x-layout.customer>
