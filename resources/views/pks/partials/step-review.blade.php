<div x-show="step === 4" x-transition
    x-data="{
        previewUrl: '',
        buildPreviewUrl() {
            const payload = {
                form: this.form,
                items: this.items,
                client: this.form.client_id
                    ? (this.clients.find(c => c.id == this.form.client_id) || {})
                    : this.form.client
            };
            return '{{ route('pks.preview') }}?payload=' + encodeURIComponent(JSON.stringify(payload));
        },
        refreshPreview() {
            this.previewUrl = this.buildPreviewUrl();
        }
    }"
    >
    <h5 class="mb-3">Step 4 - Review PKS</h5>
    <div class="alert alert-info py-2 px-3 mb-3">
        Periksa detail PKS sebelum menyimpan. Preview dokumen bisa dibuka lewat tombol di bawah.
    </div>

    <div class="border rounded p-3 bg-light">
        <h6 class="mb-2">Informasi Kontrak</h6>
        <div class="table-responsive">
            <table class="table table-sm table-borderless align-middle mb-0">
                <tbody>
                    <tr>
                        <th class="text-muted fw-normal ps-0" style="width: 190px;">Judul</th>
                        <td class="fw-semibold" x-text="form.judul || '-'"></td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal ps-0">Tanggal</th>
                        <td x-text="formatDate(form.tanggal)"></td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal ps-0">No Ref</th>
                        <td x-text="form.nomor_referensi || '-'"></td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal ps-0" style="width: 190px;">Masa Berlaku PKS</th>
                        <td>
                            <span x-text="formatDate(form.tanggal)"></span>
                            <span class="text-muted"> s/d </span>
                            <span x-text="formatDate(getContractEndDate())"></span>
                            <span class="text-muted small">(akhir penyiaran + 20 hari)</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <hr>
        <h6 class="mb-2">Client</h6>
        <div class="table-responsive">
            <table class="table table-sm table-borderless align-middle mb-0">
                <tbody>
                    <tr>
                        <th class="text-muted fw-normal ps-0" style="width: 190px;">Nama Client</th>
                        <td class="fw-semibold" x-text="reviewClient().nama ? (form.client_id ? reviewClient().nama : `Client Baru (${reviewClient().nama})`) : '-'"></td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal ps-0">Jenis Client</th>
                        <td x-text="reviewClient().jenis_klien || '-'"></td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal ps-0">Narahubung</th>
                        <td>
                            <div class="fw-semibold" x-text="reviewClient().nama_narahubung || '-'"></div>
                            <div class="text-muted small">
                                <span x-text="reviewClient().no_narahubung || '-'"></span>
                                <span class="mx-1">&bull;</span>
                                <span x-text="reviewClient().email || '-'"></span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal ps-0">Penanggung Jawab</th>
                        <td>
                            <div class="fw-semibold" x-text="reviewClient().nama_penanggung_jawab || '-'"></div>
                            <div class="text-muted small" x-text="reviewClient().jabatan || '-'"></div>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal ps-0">Alamat</th>
                        <td class="text-wrap" x-text="reviewClient().alamat || '-'"></td>
                    </tr>
                    <tr x-show="reviewClient().catatan">
                        <th class="text-muted fw-normal ps-0">Catatan</th>
                        <td class="text-wrap" x-text="reviewClient().catatan"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <hr>
        <h6 class="mb-2">Detail Jasa Penyiaran</h6>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 44px;">No.</th>
                        <th>Jasa</th>
                        <th class="px-3" style="width: 170px;">Siaran</th>
                        <th class="px-3" style="width: 220px;">Periode</th>
                        <th class="text-end" style="width: 120px;">Jumlah Tayang</th>
                        <th class="text-end" style="width: 130px;">Tarif</th>
                        <th class="text-end" style="width: 140px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in items" :key="item.id">
                        <tr>
                            <td class="text-muted" x-text="index + 1"></td>
                            <td class="fw-semibold" x-text="getLayananName(item.katalog_id)"></td>
                            <td class="text-nowrap px-3">
                                <span x-text="item.channel ? item.channel.toUpperCase().replace('PRO', 'Pro ') : '-'"></span>
                                <span class="text-muted"> - </span>
                                <span x-text="item.waktu === 'prime' ? 'Prime Time' : 'Regular Time'"></span>
                            </td>
                            <td class="text-nowrap px-3">
                                <span x-text="item.tanggal_mulai || '-'"></span>
                                <span class="text-muted"> s/d </span>
                                <span x-text="item.tanggal_selesai || '-'"></span>
                            </td>
                            <td class="text-end">
                                <span x-text="item.qty || 0"></span>
                                <span class="text-muted small"> kali</span>
                            </td>
                            <td class="text-end text-nowrap">Rp <span x-text="formatRp(item.tarif)"></span></td>
                            <td class="text-end fw-semibold text-nowrap">Rp <span x-text="formatRp(item.subtotal)"></span></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <hr>
        <p class="mb-0 fs-5 text-nowrap"><strong>Total Nilai PKS: Rp <span x-text="formatRp(grandTotal)"></span></strong></p>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <button type="button" class="btn btn-light border" @click="step--">Kembali</button>
        <div class="d-flex gap-2">
            <button
                type="button"
                class="btn btn-outline-primary"
                @click="refreshPreview(); window.open(previewUrl, '_blank')"
            >
                Preview Dokumen
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Simpan PKS
            </button>
        </div>
    </div>
</div>
