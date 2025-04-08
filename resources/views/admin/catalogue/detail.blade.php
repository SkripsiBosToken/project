<x-layout.admin-v2>
    <form action="{{ route('data.katalog.update', $data['id']) }}" method="POST" enctype="multipart/form-data">
        @csrf
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

                                <div class="form-group d-flex flex-wrap gap-2" id="photo-container-{{ $key }}">
                                    @foreach (json_decode($variant['photo']) as $index => $photo)
                                        <div class="position-relative d-inline-block"
                                            id="img-wrapper-{{ $key }}-{{ $index }}">
                                            <img class="border border-primary rounded" style="width:85px; height:85px;"
                                                src="{{ $photo }}">
                                            <button type="button"
                                                onclick="removeImage('img-wrapper-{{ $key }}-{{ $index }}')"
                                                class="btn btn-danger btn-sm position-absolute top-0 end-0 translate-middle p-1 rounded-circle">&times;</button>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-2">
                                    <input type="file" accept="image/*" multiple
                                        name="variants[{{ $key }}][photos][]" style="display:none;"
                                        id="file-input-{{ $key }}"
                                        onchange="previewImage(event, {{ $key }})">
                                    <button type="button" class="btn btn-sm btn-primary"
                                        onclick="document.getElementById('file-input-{{ $key }}').click()">Tambah
                                        Gambar</button>
                                </div>

                                <div class="form-group mt-3">
                                    <label>Nama</label>
                                    <input type="text" class="form-control"
                                        name="variants[{{ $key }}][name_type]"
                                        value="{{ $variant['name_type'] }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea class="form-control" name="variants[{{ $key }}][description]">{{ $variant['description'] }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label>Price</label>
                                    <input type="number" class="form-control"
                                        name="variants[{{ $key }}][price]" value="{{ $variant['price'] }}"
                                        required>
                                </div>

                                <div class="form-group">
                                    <label>Stock</label>
                                    <input type="number" class="form-control"
                                        name="variants[{{ $key }}][stock]" value="{{ $variant['stock'] }}"
                                        required>
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
                            <div class="form-group d-flex flex-wrap gap-2" id="photo-container-__INDEX__"></div>

                            <div class="mt-2">
                                <input type="file" accept="image/*" multiple name="new_variants[__INDEX__][photos][]"
                                    style="display:none;" id="file-input-__INDEX__"
                                    onchange="previewImage(event, __INDEX__)">
                                <button type="button" class="btn btn-sm btn-primary"
                                    onclick="document.getElementById('file-input-__INDEX__').click()">Tambah
                                    Gambar</button>
                            </div>

                            <div class="form-group mt-3">
                                <label>Nama</label>
                                <input type="text" class="form-control" name="new_variants[__INDEX__][name_type]"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea class="form-control" name="new_variants[__INDEX__][description]" required></textarea>
                            </div>

                            <div class="form-group">
                                <label>Price</label>
                                <input type="number" class="form-control" name="new_variants[__INDEX__][price]"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>Stock</label>
                                <input type="number" class="form-control" name="new_variants[__INDEX__][stock]"
                                    required>
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

    function removeImage(wrapperId) {
        const el = document.getElementById(wrapperId);
        if (el) el.remove();
    }

    function previewImage(event, key) {
        const files = event.target.files;
        const container = document.getElementById(`photo-container-${key}`);

        Array.from(files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const id = `img-wrapper-${key}-${container.querySelectorAll("img").length}`;
                const div = document.createElement("div");
                div.className = "position-relative d-inline-block";
                div.id = id;
                div.innerHTML = `
                    <img class="border border-primary rounded" style="width:85px; height:85px;" src="${e.target.result}">
                    <button type="button" onclick="removeImage('${id}')" class="btn btn-danger btn-sm position-absolute top-0 end-0 translate-middle p-1 rounded-circle">&times;</button>
                `;
                container.appendChild(div);
            };
            reader.readAsDataURL(file);
        });

        event.target.value = '';
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
