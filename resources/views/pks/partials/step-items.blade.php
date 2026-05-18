<div x-show="step === 3" x-transition>
                    <h5 class="mb-3">Step 3 - Item PKS</h5>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Katalog</th>
                                    <th>Waktu</th>
                                    <th>Channel</th>
                                    <th>Periode Tayang</th>
                                    <th style="width: 100px;">Qty</th>
                                    <th style="width: 150px;">Tarif</th>
                                    <th style="width: 150px;">Subtotal</th>
                                    <th style="width: 80px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- LOOPING ALPINE JS -->
                                <template x-for="(item, index) in items" :key="item.id">
                                    <tr>
                                        <td>
                                            <select :name="`items[${index}][katalog_id]`" x-model="item.katalog_id" @change="calculateRow(item)" class="form-control">
                                                <option value="">-- Pilih --</option>
                                                @foreach($katalogs as $k)
                                                    <option value="{{ $k->id }}">{{ $k->nama_layanan }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select :name="`items[${index}][waktu]`" x-model="item.waktu" @change="calculateRow(item)" class="form-control">
                                                <option value="regular">Regular</option>
                                                <option value="prime">Prime</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select :name="`items[${index}][channel]`" x-model="item.channel" class="form-control">
                                                <option value="pro1">Pro 1</option>
                                                <option value="pro2">Pro 2</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="date" name="items[][tanggal_mulai]" class="form-control">
                                            <small>s/d</small>
                                            <input type="date" name="items[][tanggal_selesai]" class="form-control">
                                        </td>
                                        <td><input type="number" :name="`items[${index}][qty]`" x-model.number="item.qty" @input="calculateRow(item)" class="form-control" min="1"></td>
                                        <td><input type="number" :name="`items[${index}][tarif]`" x-model.number="item.tarif" class="form-control" readonly></td>
                                        <td><input type="text" class="form-control" :value="formatRp(item.subtotal)" readonly></td>
                                        <td><button type="button" class="btn btn-danger btn-sm" @click="removeItem(index)">Hapus</button></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-outline-primary mb-3" @click="addItem()">Tambah Item</button>

                    <div class="mb-4">
                        <h5>Total: Rp <span x-text="formatRp(grandTotal)"></span></h5>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" @click="step--">Back</button>
                        <button type="button" class="btn btn-primary" @click="nextStep()">Next</button>
                    </div>
                </div>
