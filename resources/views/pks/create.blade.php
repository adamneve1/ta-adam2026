@extends('layouts.app')

@section('content')
<!-- Memanggil Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<!-- Mencegah form berkedip saat loading Alpine -->
<style> [x-cloak] { display: none !important; } </style>

<div class="container" x-data="pksForm()" x-cloak>
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="validationToast" class="toast text-bg-danger border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body" x-text="toastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-1">Buat PKS</h3>
                    <p class="text-muted mb-0">Lengkapi informasi kontrak, client, item, dan tinjau ulang sebelum menyimpan.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <strong>Berhasil!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

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

            <!-- Progress Bar -->
            <div class="mb-4">
                <div class="progress" style="height: 1.25rem;">
                    <div class="progress-bar bg-success" :style="`width: ${(step / totalSteps) * 100}%`" x-text="`Step ${step} dari ${totalSteps}`"></div>
                </div>
            </div>

            <form action="{{ route('pks.store') }}" method="POST">
                @csrf

                @include('pks.partials.step-kontrak')
@include('pks.partials.step-client')
@include('pks.partials.step-items')
@include('pks.partials.step-review')
            </form>
        </div>
    </div>
</div>  
               

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('pksForm', () => ({
        step: 1,
        totalSteps: 4,
        toastMessage: '',

        
        // Data dari Laravel DB
        clients: @json($clients),
        katalogMap: {
            @foreach($katalogs as $k)
                "{{ $k->id }}": @json($k->nama_layanan),
            @endforeach
        },
        tarifMap: {
            @foreach($tarifs as $t)
                "{{ $t->katalog_id }}_{{ $t->waktu }}": {{ $t->tarif }},
            @endforeach
        },

        // State Form Utama (Mengambil nilai old() jika gagal submit)
        form: {
            judul: @json(old('judul', '')),
            nomor_referensi: @json(old('nomor_referensi', '')),
            tanggal: @json(old('tanggal', '')),
            deskripsi: @json(old('deskripsi', '')),
            client_id: @json(old('client_id', '')),
            client: {
                jenis_klien: @json(old('client.jenis_klien', '')),
                nama: @json(old('client.nama', '')),
                nama_narahubung: @json(old('client.nama_narahubung', '')),
                no_narahubung: @json(old('client.no_narahubung', '')),
                email: @json(old('client.email', '')),
                sameAsContact: false,
                nama_penanggung_jawab: @json(old('client.nama_penanggung_jawab', '')),
                jabatan: @json(old('client.jabatan', '')),
                alamat: @json(old('client.alamat', '')),
                catatan: @json(old('client.catatan', '')),
            }
        },

        // State Items
        items: [],
        

        init() {
            // Ambil array old('items') jika ada
            let oldItems = @json(old('items', []));
            
            // Konversi old() menjadi array Alpine
            if (Object.keys(oldItems).length > 0) {
                this.items = Array.isArray(oldItems) ? oldItems : Object.values(oldItems);
                this.items.forEach(item => {
                    item.id = Date.now() + Math.random();
                    this.calculateRow(item);
                });
            } else {
                this.addItem(); // Baris kosong awal
            }
        },

        addItem() {
            this.items.push({
                id: Date.now() + Math.random(),
                katalog_id: '',
                waktu: 'regular',
                channel: 'pro1',
                tanggal_mulai: '',
                tanggal_selesai: '',
                qty: 1,
                tarif: 0,
                subtotal: 0
            });
        },

        removeItem(index) {
            this.items.splice(index, 1);
        },

        calculateRow(item) {
            const key = `${item.katalog_id}_${item.waktu}`;
            item.tarif = this.tarifMap[key] || 0;
            item.subtotal = (item.qty || 0) * item.tarif;
        },

        get grandTotal() {
            return this.items.reduce((sum, item) => sum + (item.subtotal || 0), 0);
        },

        formatRp(value) {
            return new Intl.NumberFormat('id-ID').format(value || 0);
        },

        copyContact() {
            if (this.form.client.sameAsContact) {
                this.form.client.nama_penanggung_jawab = this.form.client.nama_narahubung;
                this.form.client.jabatan = 'Narahubung';
            } else {
                this.form.client.nama_penanggung_jawab = '';
                this.form.client.jabatan = '';
            }
        },

        getClientName() {
            if (this.form.client_id) {
                const c = this.clients.find(c => c.id == this.form.client_id);
                return c ? c.nama : '-';
            }
            return this.form.client.nama ? `Client Baru (${this.form.client.nama})` : '-';
        },

        getClientField(field) {
            if (this.form.client_id) {
                const c = this.clients.find(c => c.id == this.form.client_id);
                return c && c[field] ? c[field] : '-';
            }
            return this.form.client[field] ? this.form.client[field] : '-';
        },

        getLayananName(katalogId) {
            return this.katalogMap[katalogId] || '-';
        },

        isEmpty(value) {
            return !value || value.toString().trim() === '';
        },

        showToast(message) {
            this.toastMessage = message;

            this.$nextTick(() => {
                const toastElement = document.getElementById('validationToast');
                const toast = bootstrap.Toast.getOrCreateInstance(toastElement);
                toast.show();
            });
        },

        validateStepOne() {
            if (this.isEmpty(this.form.judul) || this.isEmpty(this.form.tanggal)) {
                this.showToast('Judul dan Tanggal wajib diisi!');
                return false;
            }

            return true;
        },

        validateStepTwo() {
            if (this.form.client_id) {
                return true;
            }

            const client = this.form.client;
            const requiredFields = [
                client.jenis_klien,
                client.nama,
                client.nama_narahubung,
                client.no_narahubung,
                client.email,
                client.nama_penanggung_jawab,
                client.jabatan,
                client.alamat,
            ];

            if (requiredFields.some(field => this.isEmpty(field))) {
                this.showToast('Data client wajib diisi semua, kecuali catatan/deskripsi.');
                return false;
            }

            return true;
        },

        validateStepThree() {
            if (this.items.length === 0) {
                this.showToast('Minimal 1 item PKS harus ada!');
                return false;
            }

            return true;
        },

        nextStep() {
            if (this.step === 1 && !this.validateStepOne()) {
                return;
            }
            if (this.step === 2 && !this.validateStepTwo()) {
                return;
            }
            if (this.step === 3 && !this.validateStepThree()) {
                return;
            }
            if (this.step < this.totalSteps) this.step++;
        }
    }));
});
</script>

@endsection
