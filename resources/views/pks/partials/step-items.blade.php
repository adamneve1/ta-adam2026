<div x-show="step === 3" x-transition>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
        <div>
            <h5 class="mb-1">Step 3 - Detail Jasa Penyiaran</h5>
            <p class="text-muted small mb-0">Tambahkan jasa, periode tayang, jumlah tayang, dan nilai jasa.</p>
        </div>

        <button type="button" class="btn btn-outline-primary btn-sm align-self-start" @click="addItem()">
            <i class="bi bi-plus-circle me-1"></i>
            Tambah Jasa
        </button>
    </div>

    <div class="table-responsive mb-3">
        <table class="table table-sm table-bordered align-middle mb-0" style="min-width: 1120px;">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width: 48px;">No.</th>
                    <th style="width: 280px;">Jasa <span class="text-danger">*</span></th>
                    <th style="width: 110px;">Waktu <span class="text-danger">*</span></th>
                    <th style="width: 110px;">Channel <span class="text-danger">*</span></th>
                    <th style="width: 280px;">Alokasi Waktu <span class="text-danger">*</span></th>
                    <th class="text-end" style="width: 110px;">Jumlah Tayang <span class="text-danger">*</span></th>
                    <th class="text-end" style="width: 120px;">Tarif</th>
                    <th class="text-end" style="width: 130px;">Total</th>
                    <th class="text-center" style="width: 64px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(item, index) in items" :key="item.id">
                    <tr>
                        <td class="text-center text-muted" x-text="index + 1"></td>
                        <td>
                            <select :name="`items[${index}][katalog_id]`" x-model="item.katalog_id" @change="calculateRow(item)" class="form-select form-select-sm">
                                <option value="">-- Pilih jasa --</option>
                                @foreach($katalogs as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_layanan }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select :name="`items[${index}][waktu]`" x-model="item.waktu" @change="calculateRow(item)" class="form-select form-select-sm">
                                <option value="regular">Regular</option>
                                <option value="prime">Prime</option>
                            </select>
                        </td>
                        <td>
                            <select :name="`items[${index}][channel]`" x-model="item.channel" class="form-select form-select-sm">
                                <option value="pro1">Pro 1</option>
                                <option value="pro2">Pro 2</option>
                            </select>
                        </td>
                        <td>
                            <div class="input-group input-group-sm flex-nowrap">
                                <input type="date" :name="`items[${index}][tanggal_mulai]`" x-model="item.tanggal_mulai" class="form-control" title="Tanggal mulai" aria-label="Tanggal mulai">
                                <span class="input-group-text">to</span>
                                <input type="date" :name="`items[${index}][tanggal_selesai]`" x-model="item.tanggal_selesai" class="form-control" title="Tanggal selesai" aria-label="Tanggal selesai">
                            </div>
                        </td>
                        <td>
                            <input type="number" :name="`items[${index}][qty]`" x-model.number="item.qty" @input="calculateRow(item)" class="form-control form-control-sm text-end" min="1">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm text-end" :value="formatRp(item.tarif)" readonly>
                            <input type="hidden" :name="`items[${index}][tarif]`" :value="item.tarif">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm text-end fw-semibold" :value="formatRp(item.subtotal)" readonly>
                        </td>
                        <td class="text-center">
                            <button
                                type="button"
                                class="btn btn-outline-danger btn-sm"
                                @click="removeItem(index)"
                                :disabled="items.length === 1"
                                aria-label="Hapus jasa"
                            >
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mb-4">
        <div class="border rounded bg-light px-3 py-2 text-end" style="min-width: 220px;">
            <div class="text-muted small">Total Nilai PKS</div>
            <div class="fs-5 fw-semibold">Rp <span x-text="formatRp(grandTotal)"></span></div>
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <button type="button" class="btn btn-secondary" @click="step--">Back</button>
        <button type="button" class="btn btn-primary" @click="nextStep()">Next</button>
    </div>
</div>
