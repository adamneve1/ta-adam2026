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
