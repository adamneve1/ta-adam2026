    @extends('layouts.app')

    @section('content')

    <div class="container">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="mb-1">Buat PKS</h3>
                        <p class="text-muted mb-0">Lengkapi informasi kontrak, client, item, dan tinjau ulang sebelum menyimpan.</p>
                    </div>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <strong>Perbaiki kesalahan berikut:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-4">
                    <div class="progress" style="height: 1.25rem;">
                        <div id="form-progress" class="progress-bar bg-success" role="progressbar" style="width: 25%">Step 1 dari 4</div>
                    </div>
                </div>

                <form action="{{ route('pks.store') }}" method="POST">
                    @csrf

                    <div class="step-pane" id="step1">
                        <h5 class="mb-3">Step 1 - Informasi Kontrak</h5>

                        <div class="mb-3">
                            <label class="form-label">Judul Kontrak</label>
                            <input type="text" name="judul" value="{{ old('judul') }}" class="form-control" placeholder="Masukkan judul kontrak" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nomor Referensi</label>
                            <input type="text" name="nomor_referensi" value="{{ old('nomor_referensi') }}" class="form-control" placeholder="Contoh: PKS-2026-001">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" value="{{ old('tanggal') }}" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="4" placeholder="Deskripsikan ringkas PKS">{{ old('deskripsi') }}</textarea>
                        </div>

                        <button type="button" class="btn btn-primary" onclick="nextStep(1)">Next</button>
                    </div>

                    <div class="step-pane" id="step2" style="display:none;">
                        <h5 class="mb-3">Step 2 - Client</h5>

                        <div class="mb-4">
                            <label class="form-label">Pilih Client yang sudah terdaftar</label>
                            <select name="client_id" class="form-control">
                                <option value="">-- pilih client --</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>{{ $c->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="border p-3 rounded mb-4 bg-white">
                            <h6 class="mb-3">Input Client Baru</h6>

                            <div class="mb-3">
                                <label class="form-label">Jenis Klien</label>
                                <select name="client[jenis_klien]" class="form-control">
                                    <option value="">-- pilih jenis klien --</option>
                                    @foreach(['Instansi Pemerintahan','Perusahaan Swasta','BUMN','BUMD','Lembaga','Organisasi Nirlaba','Perorangan'] as $type)
                                        <option value="{{ $type }}" {{ old('client.jenis_klien') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Klien</label>
                                <input type="text" name="client[nama]" value="{{ old('client.nama') }}" class="form-control" placeholder="Contoh: PT Radio Indonesia">
                            </div>

                            <hr>
                            <h6 class="mb-3">Narahubung</h6>

                            <div class="mb-3">
                                <input type="text" name="client[nama_narahubung]" value="{{ old('client.nama_narahubung') }}" class="form-control" placeholder="Nama Narahubung">
                            </div>
                            <div class="mb-3">
                                <input type="text" name="client[no_narahubung]" value="{{ old('client.no_narahubung') }}" class="form-control" placeholder="No HP">
                            </div>
                            <div class="mb-3">
                                <input type="email" name="client[email]" value="{{ old('client.email') }}" class="form-control" placeholder="Email">
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" id="sameAsContact" class="form-check-input" onchange="copyContact()">
                                <label class="form-check-label" for="sameAsContact">Samakan nama penanggung jawab dengan narahubung</label>
                            </div>

                            <h6 class="mb-3">Penanggung Jawab</h6>

                            <div class="mb-3">
                                <input type="text" name="client[nama_penanggung_jawab]" value="{{ old('client.nama_penanggung_jawab') }}" class="form-control" placeholder="Nama Penanggung Jawab">
                            </div>
                            <div class="mb-3">
                                <input type="text" name="client[jabatan]" value="{{ old('client.jabatan') }}" class="form-control" placeholder="Jabatan">
                            </div>

                            <div class="mb-3">
                                <textarea name="client[alamat]" class="form-control" rows="3" placeholder="Alamat lengkap">{{ old('client.alamat') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <textarea name="client[catatan]" class="form-control" rows="2" placeholder="Catatan tambahan">{{ old('client.catatan') }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="prevStep(2)">Back</button>
                            <button type="button" class="btn btn-primary" onclick="nextStep(2)">Next</button>
                        </div>
                    </div>

                    <div class="step-pane" id="step3" style="display:none;">
                        <h5 class="mb-3">Step 3 - Item PKS</h5>

                        <div class="table-responsive mb-3">
                            <table class="table table-bordered align-middle" id="items-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Katalog</th>
                                        <th>Waktu</th>
                                        <th>Channel</th>
                                        <th style="width: 100px;">Qty</th>
                                        <th style="width: 150px;">Tarif</th>
                                        <th style="width: 150px;">Subtotal</th>
                                        <th style="width: 110px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(old('items'))
                                        @foreach(old('items') as $item)
                                            <tr>
                                                <td>
                                                    <select name="items[][katalog_id]" class="form-control" onchange="calculateRow(this)">
                                                        @foreach($katalogs as $k)
                                                            <option value="{{ $k->id }}" {{ isset($item['katalog_id']) && $item['katalog_id'] == $k->id ? 'selected' : '' }}>{{ $k->nama_layanan }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="items[][waktu]" class="form-control" onchange="calculateRow(this)">
                                                        <option value="regular" {{ ($item['waktu'] ?? '') === 'regular' ? 'selected' : '' }}>Regular</option>
                                                        <option value="prime" {{ ($item['waktu'] ?? '') === 'prime' ? 'selected' : '' }}>Prime</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="items[][channel]" class="form-control">
                                                        <option value="pro1" {{ ($item['channel'] ?? '') === 'pro1' ? 'selected' : '' }}>Pro 1</option>
                                                        <option value="pro2" {{ ($item['channel'] ?? '') === 'pro2' ? 'selected' : '' }}>Pro 2</option>
                                                    </select>
                                                </td>
                                                <td><input type="number" name="items[][qty]" value="{{ $item['qty'] ?? '' }}" class="form-control" min="0" oninput="calculateRow(this)"></td>
                                                <td><input type="number" name="items[][tarif]" value="{{ $item['tarif'] ?? '' }}" class="form-control" min="0" step="0.01" oninput="calculateRow(this)"></td>
                                                <td><input type="text" class="form-control subtotal" readonly value="{{ isset($item['qty'],$item['tarif']) ? $item['qty'] * $item['tarif'] : '' }}"></td>
                                                <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Hapus</button></td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-primary" onclick="addRow()">Tambah Item</button>
                        </div>

                        <div class="mb-4">
                            <h5>Total: Rp <span id="total">0</span></h5>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="prevStep(3)">Back</button>
                            <button type="button" class="btn btn-primary" onclick="nextStep(3)">Next</button>
                        </div>
                    </div>

                    <div class="step-pane" id="step4" style="display:none;">
                        <h5 class="mb-3">Step 4 - Review PKS</h5>

                        <div id="review-content" class="border rounded p-3 bg-light"></div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary" onclick="prevStep(4)">Back</button>
                            <button type="submit" class="btn btn-success">Simpan PKS</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
const tarifMap = {
@foreach($tarifs as $t)
    "{{ $t->katalog_id }}_{{ $t->waktu }}": {{ $t->tarif }},
@endforeach
};
</script>
    <script>
    const totalSteps = 4;

    function updateProgress(step) {
        const percent = Math.round((step / totalSteps) * 100);
        const bar = document.getElementById('form-progress');
        bar.style.width = percent + '%';
        bar.textContent = `Step ${step} dari ${totalSteps}`;
    }

    function showStep(step) {
        for (let i = 1; i <= totalSteps; i++) {
            const pane = document.getElementById(`step${i}`);
            pane.style.display = i === step ? 'block' : 'none';
        }
        updateProgress(step);
    }

    function nextStep(step) {
        if (step === 1) {
            const judul = document.querySelector('[name="judul"]').value.trim();
            const tanggal = document.querySelector('[name="tanggal"]').value;

            if (!judul) {
                alert('Judul kontrak wajib diisi.');
                return;
            }

            if (!tanggal) {
                alert('Tanggal PKS wajib diisi.');
                return;
            }
        }

        if (step === 3) {
            if (document.querySelectorAll('#items-table tbody tr').length === 0) {
                alert('Tambahkan minimal satu item PKS.');
                return;
            }
            loadReview();
        }

        showStep(step + 1);
    }

    function prevStep(step) {
        showStep(step - 1);
    }

    function addRow() {
    const tableBody = document.querySelector('#items-table tbody');
    const row = document.createElement('tr');

    row.innerHTML = `
        <td>
            <select name="items[][katalog_id]" class="form-control" onchange="calculateRow(this)">
                @foreach($katalogs as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_layanan }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <select name="items[][waktu]" class="form-control" onchange="calculateRow(this)">
                <option value="regular">Regular</option>
                <option value="prime">Prime</option>
            </select>
        </td>
        <td>
            <select name="items[][channel]" class="form-control">
                <option value="pro1">Pro 1</option>
                <option value="pro2">Pro 2</option>
            </select>
        </td>
        <td>
            <input type="number" name="items[][qty]" class="form-control" min="1" value="1" oninput="calculateRow(this)">
        </td>
        <td>
            <input type="number" name="items[][tarif]" class="form-control" readonly>
        </td>
        <td>
            <input type="text" class="form-control subtotal" readonly>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Hapus</button>
        </td>
    `;

    tableBody.appendChild(row);
    }

    function calculateRow(element) {
    const row = element.closest('tr');

    const katalog = row.querySelector('[name*="[katalog_id]"]').value;
    const waktu = row.querySelector('[name*="[waktu]"]').value;

    const key = katalog + '_' + waktu;

    const tarif = tarifMap[key] || 0;

    row.querySelector('[name*="[tarif]"]').value = tarif;

    const qty = parseFloat(row.querySelector('[name*="[qty]"]').value) || 0;
    const subtotal = qty * tarif;

    row.querySelector('.subtotal').value = subtotal.toLocaleString('id-ID');

    calculateTotal();
}

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('#items-table .subtotal').forEach(el => {
            total += parseFloat(el.value.replace(/\./g, '').replace(/,/g, '.')) || 0;
        });
        document.getElementById('total').innerText = total.toLocaleString('id-ID');
    }

    function loadReview() {
        let html = '';

        html += '<h6>Informasi Kontrak</h6>';
        html += `<p><strong>Judul:</strong> ${document.querySelector('[name="judul"]').value}</p>`;
        html += `<p><strong>Nomor Referensi:</strong> ${document.querySelector('[name="nomor_referensi"]').value || '-'}<br><strong>Tanggal:</strong> ${document.querySelector('[name="tanggal"]').value}</p>`;
        html += `<p><strong>Deskripsi:</strong> ${document.querySelector('[name="deskripsi"]').value || '-'}</p>`;

        html += '<hr>';
        html += '<h6>Client</h6>';
        const clientSelect = document.querySelector('[name="client_id"]');
        const clientText = clientSelect.options[clientSelect.selectedIndex]?.text || 'Client baru';
        html += `<p><strong>Client:</strong> ${clientText}</p>`;
        html += `<p><strong>Jenis Klien:</strong> ${document.querySelector('[name="client[jenis_klien]"]').value || '-'}<br><strong>Nama Klien:</strong> ${document.querySelector('[name="client[nama]"]').value || '-'}</p>`;
        html += `<p><strong>Narahubung:</strong> ${document.querySelector('[name="client[nama_narahubung]"]').value || '-'} / ${document.querySelector('[name="client[no_narahubung]"]').value || '-'} / ${document.querySelector('[name="client[email]"]').value || '-'}</p>`;
        html += `<p><strong>Penanggung Jawab:</strong> ${document.querySelector('[name="client[nama_penanggung_jawab]"]').value || '-'} (${document.querySelector('[name="client[jabatan]"]').value || '-'})</p>`;
        html += `<p><strong>Alamat:</strong> ${document.querySelector('[name="client[alamat]"]').value || '-'}<br><strong>Catatan:</strong> ${document.querySelector('[name="client[catatan]"]').value || '-'}</p>`;

        html += '<hr>';
        html += '<h6>Items</h6>';

        let total = 0;
        document.querySelectorAll('#items-table tbody tr').forEach((row, index) => {
            const katalog = row.querySelector('[name*="[katalog_id]"] option:checked').text;
            const waktu = row.querySelector('[name*="[waktu]"] option:checked').text;
            const channel = row.querySelector('[name*="[channel]"] option:checked').text;
            const qty = row.querySelector('[name*="[qty]"]').value || 0;
            const tarif = row.querySelector('[name*="[tarif]"]').value || 0;
            const subtotal = parseFloat(qty) * parseFloat(tarif);
            total += subtotal;

            html += `<p><strong>Item ${index + 1}:</strong> ${katalog} • ${waktu} • ${channel}<br>Qty: ${qty} • Tarif: Rp ${parseFloat(tarif).toLocaleString('id-ID')} • Subtotal: Rp ${subtotal.toLocaleString('id-ID')}</p>`;
        });

        html += `<hr><p class="mb-0"><strong>Total PKS:</strong> Rp ${total.toLocaleString('id-ID')}</p>`;
        document.getElementById('review-content').innerHTML = html;
    }

    function copyContact() {
        const checked = document.getElementById('sameAsContact').checked;
        const namaNarahubung = document.querySelector('[name="client[nama_narahubung]"]').value;

        document.querySelector('[name="client[nama_penanggung_jawab]"]').value = checked ? namaNarahubung : '';
        document.querySelector('[name="client[jabatan]"]').value = checked ? 'Narahubung' : '';
    }

    window.addEventListener('DOMContentLoaded', () => {
        showStep(1);
        calculateTotal();

        @if(old('items'))
            document.querySelectorAll('#items-table tbody tr').forEach(row => calculateRow(row.querySelector('[name*="[qty]"]')));
        @else
            addRow();
        @endif
    });
    </script>

    

    @endsection