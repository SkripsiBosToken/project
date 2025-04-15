@props([
    'images' => [],
])

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<div class="flex flex-col md:flex-row gap-8 items-center justify-center" x-data="imageSelector({{ json_encode($images) }})">

    <div class="flex flex-col gap-4">
        <img :src="mainImage" class="w-32 h-32 md:w-64 md:h-64 object-cover rounded-lg" alt="main">

        <div class="flex gap-2">
            <template x-for="(image, index) in images" :key="index">
                <div :class="mainImage === image ? 'border border-spacing-1 border-primary rounded-md' : 'border border-spacing-1 border-primary-gray rounded-md'" @click="mainImage = image">
                    <img :src="image" class="w-12 md:w-16 h-12 md:h-16 rounded-lg aspect-square" alt="images" />
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    function imageSelector(images) {
        return {
            images: images.map(image => `{{ asset('${image}') }}`) || [],
            mainImage: images.length > 0 ? `{{ asset('${images[0]}') }}` : '',
        }
    }

</script>