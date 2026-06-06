<div x-show="step === 2" x-transition>
    <h5 class="mb-3">Step 2 - Client</h5>

    <div class="btn-group mb-4" role="group" aria-label="Mode input client">
        <input type="radio" class="btn-check" name="client_mode" id="clientModeRegistered" value="registered" x-model="clientMode" @change="switchClientMode('registered')">
        <label class="btn btn-outline-primary" for="clientModeRegistered">Client Terdaftar</label>

        <input type="radio" class="btn-check" name="client_mode" id="clientModeNew" value="new" x-model="clientMode" @change="switchClientMode('new')">
        <label class="btn btn-outline-primary" for="clientModeNew">Client Baru</label>
    </div>

    <div x-show="clientMode === 'registered'" x-transition class="mb-4">
        <label class="form-label">Pilih Client yang sudah terdaftar <span class="text-danger">*</span></label>
        <select name="client_id" x-model="form.client_id" class="form-select" :disabled="clientMode !== 'registered'">
            <option value="">-- Pilih Client --</option>
            @foreach($clients as $c)
                <option value="{{ $c->id }}">{{ $c->nama }}</option>
            @endforeach
        </select>

        <div class="border rounded p-3 mt-3 bg-light" x-show="selectedClient()" x-transition>
            <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                <div>
                    <h6 class="mb-1" x-text="selectedClient()?.nama || '-'"></h6>
                    <div class="text-muted small" x-text="selectedClient()?.jenis_klien || 'Jenis client belum diisi'"></div>
                </div>
                <span class="badge bg-success">Terpilih</span>
            </div>

            <div class="row g-3 small">
                <div class="col-md-6">
                    <div class="text-muted">Narahubung</div>
                    <div class="fw-semibold" x-text="selectedClient()?.nama_narahubung || '-'"></div>
                    <div x-text="selectedClient()?.no_narahubung || '-'"></div>
                    <div x-text="selectedClient()?.email || '-'"></div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted">Penanggung Jawab</div>
                    <div class="fw-semibold" x-text="selectedClient()?.nama_penanggung_jawab || '-'"></div>
                    <div x-text="selectedClient()?.jabatan || '-'"></div>
                </div>
                <div class="col-12">
                    <div class="text-muted">Alamat</div>
                    <div x-text="selectedClient()?.alamat || '-'"></div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="clientMode === 'new'" x-transition class="border p-3 rounded mb-4 bg-white">
        <h6 class="mb-1">Input Client Baru</h6>
        <p class="text-muted small mb-3">Client baru akan disimpan setelah PKS disimpan, jadi tidak ada data setengah jadi kalau form dibatalkan.</p>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Jenis Klien <span class="text-danger">*</span></label>
                <select name="client[jenis_klien]" x-model="form.client.jenis_klien" class="form-select" :disabled="clientMode !== 'new'">
                    <option value="">-- Pilih --</option>
                    @foreach(['Instansi Pemerintahan','Perusahaan Swasta','BUMN','BUMD','Lembaga','Organisasi Nirlaba','Perorangan'] as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nama Klien <span class="text-danger">*</span></label>
                <input type="text" name="client[nama]" x-model="form.client.nama" class="form-control" :disabled="clientMode !== 'new'">
            </div>
        </div>

        <hr>
        <h6 class="mb-3">Narahubung</h6>
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Nama <span class="text-danger">*</span></label>
                <input type="text" name="client[nama_narahubung]" x-model="form.client.nama_narahubung" @input="syncResponsibleFromContact()" class="form-control" :disabled="clientMode !== 'new'">
            </div>
            <div class="col-md-4">
                <label class="form-label">No HP <span class="text-danger">*</span></label>
                <input type="text" name="client[no_narahubung]" x-model="form.client.no_narahubung" class="form-control" :disabled="clientMode !== 'new'">
            </div>
            <div class="col-md-4">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="client[email]" x-model="form.client.email" class="form-control" :disabled="clientMode !== 'new'">
            </div>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" id="sameAsContact" class="form-check-input" x-model="form.client.sameAsContact" @change="copyContact()" :disabled="clientMode !== 'new'">
            <label class="form-check-label" for="sameAsContact">Samakan nama penanggung jawab dengan narahubung</label>
        </div>

        <h6 class="mb-3">Penanggung Jawab</h6>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Nama PJ <span class="text-danger">*</span></label>
                <input type="text" name="client[nama_penanggung_jawab]" x-model="form.client.nama_penanggung_jawab" class="form-control" :disabled="clientMode !== 'new'">
            </div>
            <div class="col-md-6">
                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                <input type="text" name="client[jabatan]" x-model="form.client.jabatan" class="form-control" :disabled="clientMode !== 'new'">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Alamat <span class="text-danger">*</span></label>
            <textarea name="client[alamat]" x-model="form.client.alamat" class="form-control" rows="2" :disabled="clientMode !== 'new'"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Catatan</label>
            <textarea name="client[catatan]" x-model="form.client.catatan" class="form-control" rows="2" :disabled="clientMode !== 'new'"></textarea>
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <button type="button" class="btn btn-secondary" @click="step--">Back</button>
        <button type="button" class="btn btn-primary" @click="nextStep()">Next</button>
    </div>
</div>
