<x-layout.admin-v2>
    <form action="{{ route('data.katalog.update', $data['id']) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="content mt-3">
            <div class="row">
                <!-- Kolom Produk -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header"><strong class="card-title">Data Produk</strong></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text" class="form-control" name="name" value="{{ $data['name'] }}">
                            </div>

                            <div class="form-group">
                                <label>Category</label>
                                <select name="category_id" class="form-control">
                                    <option value="{{ $data['category']['id'] }}">{{ $data['category']['name'] }}
                                    </option>
                                    @foreach ($categories as $category)
                                        @if ($category['id'] != $data['category']['id'])
                                            <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol tambah variant -->
                    <button type="button" class="btn btn-success" onclick="addVariantCard()">Tambah Variant</button>
                    <input type="hidden" name="deletedVariantIds" id="deleted-variant-ids">
                    <button type="submit" class="btn btn-primary">Simpan Semua</button>
                </div>

                <!-- Kolom Variant -->
                <div class="col-lg-6" id="variant-container">
                    @foreach ($data['product_variants'] as $key => $variant)
                        <div class="card mb-4 variant-card">
                            <div class="card-header"><strong class="card-title">Variant {{ $key + 1 }}</strong>
                            </div>
                            <div class="card-body">
                                <input type="hidden" name="variants[{{ $key }}][id]"
                                    value="{{ $variant['id'] }}">

                                <div class="form-group">
                                    <label>Foto Produk</label>

                                    <div id="preview-photos-{{ $key }}" class="d-flex flex-wrap gap-2 mb-2">
                                        @foreach (json_decode($variant['photo']) as $photoIndex => $photo)
                                            <div id="photo-wrapper-{{ $key }}-{{ $photoIndex }}"
                                                class="position-relative d-inline-block me-2 mb-2 photo-box">
                                                <img src="{{ $photo }}" class="border border-primary rounded"
                                                    style="width:85px; height:85px;">
                                                <button type="button"
                                                    onclick="deletePhoto('{{ $photo }}', '{{ $key }}', 'photo-wrapper-{{ $key }}-{{ $photoIndex }}')"
                                                    class="btn btn-sm btn-danger position-absolute top-0 end-0">
                                                    &times;
                                                </button>

                                            </div>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="variants[{{ $key }}][deletedPhotos]"
                                        id="deleted-photos-{{ $key }}">

                                    {{-- Input file untuk upload --}}
                                    <input type="file" name="variants[{{ $key }}][photos][]"
                                        id="file-input-{{ $key }}" class="form-control d-none"
                                        accept="image/*" onchange="previewImage(event, {{ $key }})" multiple>

                                    <input type="hidden" name="test_photos_upload_check" value="1">

                                    {{-- Tombol trigger upload --}}
                                    <button type="button" class="btn btn-sm btn-primary"
                                        onclick="document.getElementById('file-input-{{ $key }}').click()">
                                        Tambah Gambar
                                    </button>
                                    <small class="text-muted">Pilih beberapa gambar sekaligus menggunakan Ctrl /
                                        Shift</small>

                                </div>

                                <div class="form-group mt-3">
                                    <label>Nama</label>
                                    <input type="text" class="form-control"
                                        name="variants[{{ $key }}][name_type]"
                                        value="{{ $variant['name_type'] }}">
                                </div>

                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea class="form-control" name="variants[{{ $key }}][description]">{{ $variant['description'] }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label>Price</label>
                                    <input type="number" class="form-control"
                                        name="variants[{{ $key }}][price]" value="{{ $variant['price'] }}">
                                </div>

                                <div class="form-group">
                                    <label>Stock</label>
                                    <input type="number" class="form-control"
                                        name="variants[{{ $key }}][stock]" value="{{ $variant['stock'] }}">
                                </div>

                                <button type="button" class="btn btn-danger btn-sm mt-2"
                                    onclick="deleteVariantCard(this, '{{ $variant['id'] }}')">Hapus Variant</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Template -->
                <template id="variant-template">
                    <div class="card mb-4 variant-card">
                        <div class="card-header"><strong class="card-title">Variant Baru</strong></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Foto Produk</label>
                                <div id="preview-photos-__INDEX__" class="d-flex flex-wrap gap-2 mb-2"></div>

                                <input type="file" accept="image/*" multiple name="new_variants[__INDEX__][photos][]"
                                    id="file-input-__INDEX__" class="form-control d-none"
                                    onchange="previewImage(event, __INDEX__)">

                                <button type="button" class="btn btn-sm btn-primary"
                                    onclick="document.getElementById('file-input-__INDEX__').click()">
                                    Tambah Gambar
                                </button>
                                <small class="text-muted">Pilih beberapa gambar sekaligus menggunakan Ctrl /
                                    Shift</small>
                            </div>

                            <div class="form-group mt-3">
                                <label>Nama</label>
                                <input type="text" class="form-control" name="new_variants[__INDEX__][name_type]">
                            </div>

                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea class="form-control" name="new_variants[__INDEX__][description]"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Price</label>
                                <input type="number" class="form-control" name="new_variants[__INDEX__][price]">
                            </div>

                            <div class="form-group">
                                <label>Stock</label>
                                <input type="number" class="form-control" name="new_variants[__INDEX__][stock]">
                            </div>

                            <button type="button" class="btn btn-danger btn-sm mt-2"
                                onclick="deleteVariantCard(this)">Hapus Variant</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </form>
</x-layout.admin-v2>

<script>
    let variantCount = {{ count($data['product_variants']) }};
    let deletedIds = [];
    let deletedPhotos = {};

    function removeImage(wrapperId) {
        const el = document.getElementById(wrapperId);
        if (el) el.remove();
    }

    function deletePhoto(photoPath, variantKey, wrapperId) {
        const el = document.getElementById(wrapperId);
        if (el) el.remove();

        const input = document.getElementById(`deleted-photos-${variantKey}`);

        let current = [];
        try {
            current = JSON.parse(input.value || '[]');
        } catch (e) {
            current = [];
        }

        if (!current.includes(photoPath)) {
            current.push(photoPath);
            input.value = JSON.stringify(current);
        }

        const wrapper = document.getElementById(wrapperId);
        if (wrapper) {
            wrapper.style.display = "none";
        }
    }

    function previewImage(event, key) {
        const container = document.getElementById(`preview-photos-${key}`);
        const files = event.target.files;

        const clonedInput = event.target.cloneNode();
        clonedInput.style.display = "none";
        document.querySelector(`#variant-container`).appendChild(clonedInput);

        Array.from(files).forEach((file) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const wrapper = document.createElement('div');
                wrapper.className = "position-relative d-inline-block me-2 mb-2";
                wrapper.innerHTML = `
                <img src="${e.target.result}" class="border border-primary rounded" style="width:85px; height:85px;">
            `;
                container.appendChild(wrapper);
            };
            reader.readAsDataURL(file);
        });

        // reset file input agar onchange bisa bekerja lagi
        event.target.value = "";
    }



    function addVariantCard() {
        const template = document.getElementById('variant-template').innerHTML;
        const newHtml = template.replace(/__INDEX__/g, variantCount);
        document.getElementById('variant-container').insertAdjacentHTML('beforeend', newHtml);
        variantCount++;
    }

    function deleteVariantCard(button, variantId = null) {
        const card = button.closest('.variant-card');
        if (variantId) {
            deletedIds.push(variantId);
            document.getElementById('deleted-variant-ids').value = JSON.stringify(deletedIds);
        }
        card.remove();
    }
</script>
