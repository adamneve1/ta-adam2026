 <div x-show="step === 4" x-transition>
                    <h5 class="mb-3">Step 4 - Review PKS</h5>

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

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary" @click="step--">Back</button>
                        <button type="submit" class="btn btn-success">Simpan PKS</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>