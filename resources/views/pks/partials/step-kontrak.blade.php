<div x-show="step === 1" x-transition>
    <h5 class="mb-3">Step 1 - Informasi Kontrak</h5>

    <div class="mb-3">
        <label class="form-label">Judul Kontrak</label>
        <input type="text" name="judul" x-model="form.judul" @input="form.judul = form.judul.toUpperCase()" class="form-control text-uppercase" placeholder="Masukkan judul kontrak">
    </div>

    <div class="mb-3">
        <label class="form-label">Nomor Referensi</label>
        <input type="text" name="nomor_referensi" x-model="form.nomor_referensi" @input="form.nomor_referensi = form.nomor_referensi.toUpperCase()" class="form-control text-uppercase" placeholder="Contoh: PKS-2026-001">
    </div>

    <div class="mb-3">
        <label class="form-label">Tanggal</label>
        <input type="date" name="tanggal" x-model="form.tanggal" class="form-control" :disabled="isLocked">
        <div class="form-text" x-show="isLocked">Tanggal dikunci karena PKS sudah memiliki invoice.</div>
    </div>

    <div class="mb-3">
        <label class="form-label">Deskripsi (Opsional)</label>
        <textarea name="deskripsi" x-model="form.deskripsi" class="form-control" rows="4"></textarea>
    </div>

    <button type="button" class="btn btn-primary" @click="nextStep()">Lanjut</button>
</div>
