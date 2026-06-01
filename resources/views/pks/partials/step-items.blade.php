<div x-show="step === 3" x-transition>
    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
        <div>
            <h5 class="mb-1">Step 3 - Item PKS</h5>
            <p class="text-muted mb-0">Tambahkan layanan yang masuk ke dalam kontrak.</p>
        </div>

        <button type="button" class="btn btn-outline-primary" @click="addItem()">
            <i class="bi bi-plus-circle me-1"></i>
            Tambah Item
        </button>
    </div>

    <div class="table-responsive mb-3">
        <table class="table table-bordered align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="min-width: 220px;">Katalog</th>
                    <th style="min-width: 120px;">Waktu</th>
                    <th style="min-width: 120px;">Channel</th>
                    <th style="min-width: 250px;">Periode Tayang</th>
                    <th style="width: 90px;">Qty</th>
                    <th style="min-width: 140px;">Tarif</th>
                    <th style="min-width: 150px;">Subtotal</th>
                    <th style="width: 70px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(item, index) in items" :key="item.id">
                    <tr>
                        <td>
                            <select :name="`items[${index}][katalog_id]`" x-model="item.katalog_id" @change="calculateRow(item)" class="form-select">
                                <option value="">-- Pilih layanan --</option>
                                @foreach($katalogs as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_layanan }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select :name="`items[${index}][waktu]`" x-model="item.waktu" @change="calculateRow(item)" class="form-select">
                                <option value="regular">Regular</option>
                                <option value="prime">Prime</option>
                            </select>
                        </td>
                        <td>
                            <select :name="`items[${index}][channel]`" x-model="item.channel" class="form-select">
                                <option value="pro1">Pro 1</option>
                                <option value="pro2">Pro 2</option>
                            </select>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <input type="date" :name="`items[${index}][tanggal_mulai]`" x-model="item.tanggal_mulai" class="form-control">
                                <span class="text-muted small">s/d</span>
                                <input type="date" :name="`items[${index}][tanggal_selesai]`" x-model="item.tanggal_selesai" class="form-control">
                            </div>
                        </td>
                        <td>
                            <input type="number" :name="`items[${index}][qty]`" x-model.number="item.qty" @input="calculateRow(item)" class="form-control" min="1">
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" :name="`items[${index}][tarif]`" x-model.number="item.tarif" class="form-control" readonly>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" :value="formatRp(item.subtotal)" readonly>
                            </div>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-outline-danger btn-sm" @click="removeItem(index)" aria-label="Hapus item">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mb-4">
        <div class="border rounded bg-light px-3 py-2 text-end">
            <div class="text-muted small">Total PKS</div>
            <div class="fs-5 fw-semibold">Rp <span x-text="formatRp(grandTotal)"></span></div>
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <button type="button" class="btn btn-secondary" @click="step--">Back</button>
        <button type="button" class="btn btn-primary" @click="nextStep()">Next</button>
    </div>
</div>
