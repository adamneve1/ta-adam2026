<div x-show="step === 2" x-transition>
    <h5 class="mb-3">Step 2 - Client</h5>

    <div class="mb-4">
        <label class="form-label">Pilih Client yang sudah terdaftar</label>
        <select name="client_id" x-model="form.client_id" class="form-control">
            <option value="">-- Pilih Client --</option>
            @foreach($clients as $c)
                <option value="{{ $c->id }}">{{ $c->nama }}</option>
            @endforeach
        </select>
    </div>

    <div class="border p-3 rounded mb-4 bg-white" :class="{ 'opacity-50 pointer-events-none': form.client_id }">
        <h6 class="mb-3">Input Client Baru</h6>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Jenis Klien</label>
                <select name="client[jenis_klien]" x-model="form.client.jenis_klien" class="form-control">
                    <option value="">-- Pilih --</option>
                    @foreach(['Instansi Pemerintahan','Perusahaan Swasta','BUMN','BUMD','Lembaga','Organisasi Nirlaba','Perorangan'] as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nama Klien</label>
                <input type="text" name="client[nama]" x-model="form.client.nama" class="form-control">
            </div>
        </div>

        <hr>
        <h6 class="mb-3">Narahubung</h6>
        <div class="row mb-3">
            <div class="col-md-4"><input type="text" name="client[nama_narahubung]" x-model="form.client.nama_narahubung" class="form-control" placeholder="Nama"></div>
            <div class="col-md-4"><input type="text" name="client[no_narahubung]" x-model="form.client.no_narahubung" class="form-control" placeholder="No HP"></div>
            <div class="col-md-4"><input type="email" name="client[email]" x-model="form.client.email" class="form-control" placeholder="Email"></div>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" id="sameAsContact" class="form-check-input" x-model="form.client.sameAsContact" @change="copyContact()">
            <label class="form-check-label" for="sameAsContact">Samakan nama penanggung jawab dengan narahubung</label>
        </div>

        <h6 class="mb-3">Penanggung Jawab</h6>
        <div class="row mb-3">
            <div class="col-md-6"><input type="text" name="client[nama_penanggung_jawab]" x-model="form.client.nama_penanggung_jawab" class="form-control" placeholder="Nama PJ"></div>
            <div class="col-md-6"><input type="text" name="client[jabatan]" x-model="form.client.jabatan" class="form-control" placeholder="Jabatan"></div>
        </div>
        <div class="mb-3"><textarea name="client[alamat]" x-model="form.client.alamat" class="form-control" rows="2" placeholder="Alamat"></textarea></div>
        <div class="mb-3"><textarea name="client[catatan]" x-model="form.client.catatan" class="form-control" rows="2" placeholder="Catatan"></textarea></div>
    </div>

    <div class="d-flex justify-content-between">
        <button type="button" class="btn btn-secondary" @click="step--">Back</button>
        <button type="button" class="btn btn-primary" @click="nextStep()">Next</button>
    </div>
</div>
