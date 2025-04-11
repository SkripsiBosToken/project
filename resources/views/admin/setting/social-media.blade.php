<x-layout.admin-v2>
    <form action="{{ route('system.social-media.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="content my-3">
            <div class="d-flex flex-wrap gap-3">
                @foreach ($medias as $key => $item)
                    <div class="card">
                        <div class="card-header"><strong class="card-title">Sosial Media {{ $key + 1 }}</strong></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nama Sosial Media</label>
                                <input type="text" class="form-control" name="customers[{{ $key }}][name]"
                                    value="{{ $item['name'] }}" required>
                            </div>
                            <div class="form-group">
                                <label>Link</label>
                                <input type="text" class="form-control" name="customers[{{ $key }}][href]"
                                    value="{{ $item['href'] }}" required>
                            </div>
                            <div class="form-group">
                                <label>Logo Saat Ini</label><br>
                                <img id="logo-preview" src="{{ $item['logo'] }}" alt="Logo Saat Ini" height="80"
                                    width="80" class="mb-2 border rounded">

                                <label for="logo">Ubah Logo <small class="text-danger">* Disarankan berukuran
                                        (250x100)
                                        atau (160x160) pixel</small></label>
                                <input type="file" class="form-control" name="customers[{{ $key }}][logo]"
                                    id="logo" accept="image/*" onchange="previewLogo(event)">
                            </div>

                            <button type="button" class="btn btn-danger mt-2" onclick="deleteCard(this)">
                                <i class="fa fa-trash"></i> Hapus
                            </button>

                        </div>
                    </div>
                @endforeach
            </div>
            
            <input type="hidden" name="deleted_customer_indexes" id="deleted-customer-indexes">
            <button type="submit" class="btn btn-primary mt-3"><i class="fa fa-floppy-o mr-2" aria-hidden="true"></i>
                Simpan</button>
            <button type="button" class="btn btn-success mt-3" onclick="addCustomerCard()">
                <i class="fa fa-plus-circle mr-1" aria-hidden="true"></i> Tambah Sosial Media
            </button>

        </div>
    </form>
    <template id="customer-template">
        <div class="card">
            <div class="card-header"><strong class="card-title">Sosial Media Baru</strong></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Sosial Media</label>
                    <input type="text" class="form-control" name="new_customers[__INDEX__][name]" required>
                </div>

                <div class="form-group">
                    <label>Link</label>
                    <input type="text" class="form-control" name="new_customers[__INDEX__][href]" required>
                </div>
                <div class="form-group">
                    <label>Logo <small class="text-danger">* Disarankan (250x100) / (160x160)</small></label>
                    <input type="file" class="form-control" name="new_customers[__INDEX__][logo]" accept="image/*"
                        onchange="previewLogoDynamic(event, __INDEX__)" required>
                    <img id="logo-preview-__INDEX__" src="" alt="Preview Logo" height="80" width="80"
                        class="mt-2 border rounded d-none">
                </div>
                <button type="button" class="btn btn-danger mt-2" onclick="deleteCard(this)">
                    <i class="fa fa-trash"></i> Hapus
                </button>
            </div>
        </div>
    </template>

</x-layout.admin-v2>

<script>
    let customerIndex = {{ count($medias) }};
    let deletedIndexes = [];

    function addCustomerCard() {
        const template = document.getElementById("customer-template").innerHTML;
        const newCard = template.replace(/__INDEX__/g, customerIndex);
        document.querySelector(".d-flex.flex-wrap").insertAdjacentHTML("beforeend", newCard);
        customerIndex++;
    }

    function previewLogoDynamic(event, index) {
        const input = event.target;
        const preview = document.getElementById('logo-preview-' + index);

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    function deleteCard(button) {
        const card = button.closest('.card');

        const nameInput = card.querySelector("input[name^='customers']");

        if (nameInput) {
            const nameAttr = nameInput.getAttribute('name');
            const match = nameAttr.match(/\[(\d+)\]/);

            if (match) {
                deletedIndexes.push(match[1]);
                document.getElementById('deleted-customer-indexes').value = JSON.stringify(deletedIndexes);
            }
        }

        card.remove();
    }
</script>
