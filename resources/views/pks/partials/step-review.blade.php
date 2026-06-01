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
                            return '{{ route('pks.cetak', 'preview') }}?payload=' + encodeURIComponent(JSON.stringify(payload));
                        },
                        refreshPreview() {
                            this.previewUrl = this.buildPreviewUrl();
                        }
                    }"
                    x-effect="if (step === 4 && !previewUrl) refreshPreview()">
                    <h5 class="mb-3">Step 4 - Review PKS</h5>
                    <div class="alert alert-info py-2 px-3 mb-3">
                        Preview final menggunakan halaman cetak dan akan otomatis terbuka setelah PKS disimpan.
                    </div>

                    <div class="border rounded p-3 bg-light">
                        <h6>Informasi Kontrak</h6>
                        <p><strong>Judul:</strong> <span x-text="form.judul || '-'"></span></p>
                        <p><strong>Tanggal:</strong> <span x-text="form.tanggal || '-'"></span><br>
                           <strong>No Ref:</strong> <span x-text="form.nomor_referensi || '-'"></span></p>
                        
                        <hr>
                        <h6>Client</h6>
                        <p><strong>Client:</strong> <span x-text="getClientName()"></span></p>

                        <hr>
                        <h6>Items</h6>
                        <template x-for="(item, index) in items">
                            <p class="mb-1">
                                <strong x-text="`Item ${index + 1}:`"></strong> 
                                Qty: <span x-text="item.qty"></span> &bull; 
                                Tarif: Rp <span x-text="formatRp(item.tarif)"></span> &bull; 
                                Subtotal: Rp <span x-text="formatRp(item.subtotal)"></span>
                            </p>
                        </template>
                        <hr>
                        <p class="mb-0 fs-5"><strong>Total PKS: Rp <span x-text="formatRp(grandTotal)"></span></strong></p>
                    </div>

                    <div class="border rounded p-4 mt-3 bg-white">
                        <h6 class="mb-3">Preview Dokumen Cetak (Sesuai Template)</h6>
                        <p class="text-muted mb-3">
                            Preview ini menggunakan template cetak yang sama. Klik refresh kalau ada data yang baru diubah.
                        </p>
                        <div class="mb-3 d-flex gap-2">
                            <button
                                type="button"
                                class="btn btn-outline-secondary btn-sm"
                                @click="refreshPreview()"
                            >
                                Refresh Preview
                            </button>
                            <a
                                class="btn btn-outline-primary btn-sm"
                                target="_blank"
                                :href="previewUrl"
                            >
                                Preview PDF
                            </a>
                        </div>
                        <div class="border rounded overflow-hidden">
                            <iframe
                                :src="previewUrl ? (previewUrl + '#zoom=page-width') : 'about:blank'"
                                title="Preview Template Cetak PKS"
                                style="width:100%; height:780px; border:0;"
                            ></iframe>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary" @click="step--">Back</button>
                        <button type="submit" class="btn btn-success">Simpan &amp; Lihat Versi Cetak</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
