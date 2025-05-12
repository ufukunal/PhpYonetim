<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-tie mr-2"></i>Müşteri Ekle</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('customer/add'); ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="code">Müşteri Kodu</label>
                        <input type="text" class="form-control" id="code" name="code" required>
                    </div>
                    <div class="form-group">
                        <label for="type">Müşteri Türü</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="customer">Müşteri</option>
                            <option value="supplier">Tedarikçi</option>
                            <option value="other">Diğer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="title">Unvan</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="tax_number">Vergi Numarası</label>
                        <input type="text" class="form-control" id="tax_number" name="tax_number">
                    </div>
                    <div class="form-group">
                        <label for="tax_office">Vergi Dairesi</label>
                        <input type="text" class="form-control" id="tax_office" name="tax_office">
                    </div>
                    <div class="form-group">
                        <label for="group_id">Müşteri Grubu</label>
                        <select class="form-control select2" id="group_id" name="group_id">
                            <option value="">Seçiniz</option>
                            <?php foreach ($groups as $group): ?>
                                <option value="<?php echo $group['id']; ?>"><?php echo sanitize($group['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5>Adresler</h5>
                    <div id="address-container">
                        <div class="card mb-2 address-card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Adres Türü</label>
                                    <select class="form-control" name="addresses[0][type]" required>
                                        <option value="invoice">Fatura Adresi</option>
                                        <option value="delivery">Sevk Adresi</option>
                                        <option value="other">Diğer</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Başlık</label>
                                    <input type="text" class="form-control" name="addresses[0][title]" required>
                                </div>
                                <div class="form-group">
                                    <label>Adres</label>
                                    <textarea class="form-control" name="addresses[0][address]" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Şehir</label>
                                    <input type="text" class="form-control" name="addresses[0][city]">
                                </div>
                                <div class="form-group">
                                    <label>Ülke</label>
                                    <input type="text" class="form-control" name="addresses[0][country]">
                                </div>
                                <div class="form-group">
                                    <label>Posta Kodu</label>
                                    <input type="text" class="form-control" name="addresses[0][postal_code]">
                                </div>
                                <button type="button" class="btn btn-danger btn-sm remove-address"><i class="fas fa-trash"></i> Sil</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mb-3" id="add-address"><i class="fas fa-plus"></i> Adres Ekle</button>
                    <h5>Yetkili Kişiler</h5>
                    <div id="contact-container">
                        <div class="card mb-2 contact-card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>İsim</label>
                                    <input type="text" class="form-control" name="contacts[0][name]" required>
                                </div>
                                <div class="form-group">
                                    <label>Unvan</label>
                                    <input type="text" class="form-control" name="contacts[0][title]">
                                </div>
                                <div class="form-group">
                                    <label>Telefon</label>
                                    <input type="text" class="form-control" name="contacts[0][phone]">
                                </div>
                                <div class="form-group">
                                    <label>E-posta</label>
                                    <input type="email" class="form-control" name="contacts[0][email]">
                                </div>
                                <div class="form-group">
                                    <label>Not</label>
                                    <textarea class="form-control" name="contacts[0][note]"></textarea>
                                </div>
                                <button type="button" class="btn btn-danger btn-sm remove-contact"><i class="fas fa-trash"></i> Sil</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mb-3" id="add-contact"><i class="fas fa-plus"></i> Yetkili Ekle</button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Kaydet</button>
        </form>
    </div>
</div>
<script>
$(document).ready(function() {
    $('.select2').select2();
    let addressIndex = 1;
    let contactIndex = 1;

    $('#add-address').click(function() {
        const html = `
            <div class="card mb-2 address-card">
                <div class="card-body">
                    <div class="form-group">
                        <label>Adres Türü</label>
                        <select class="form-control" name="addresses[${addressIndex}][type]" required>
                            <option value="invoice">Fatura Adresi</option>
                            <option value="delivery">Sevk Adresi</option>
                            <option value="other">Diğer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Başlık</label>
                        <input type="text" class="form-control" name="addresses[${addressIndex}][title]" required>
                    </div>
                    <div class="form-group">
                        <label>Adres</label>
                        <textarea class="form-control" name="addresses[${addressIndex}][address]" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Şehir</label>
                        <input type="text" class="form-control" name="addresses[${addressIndex}][city]">
                    </div>
                    <div class="form-group">
                        <label>Ülke</label>
                        <input type="text" class="form-control" name="addresses[${addressIndex}][country]">
                    </div>
                    <div class="form-group">
                        <label>Posta Kodu</label>
                        <input type="text" class="form-control" name="addresses[${addressIndex}][postal_code]">
                    </div>
                    <button type="button" class="btn btn-danger btn-sm remove-address"><i class="fas fa-trash"></i> Sil</button>
                </div>
            </div>`;
        $('#address-container').append(html);
        addressIndex++;
    });

    $('#add-contact').click(function() {
        const html = `
            <div class="card mb-2 contact-card">
                <div class="card-body">
                    <div class="form-group">
                        <label>İsim</label>
                        <input type="text" class="form-control" name="contacts[${contactIndex}][name]" required>
                    </div>
                    <div class="form-group">
                        <label>Unvan</label>
                        <input type="text" class="form-control" name="contacts[${contactIndex}][title]">
                    </div>
                    <div class="form-group">
                        <label>Telefon</label>
                        <input type="text" class="form-control" name="contacts[${contactIndex}][phone]">
                    </div>
                    <div class="form-group">
                        <label>E-posta</label>
                        <input type="email" class="form-control" name="contacts[${contactIndex}][email]">
                    </div>
                    <div class="form-group">
                        <label>Not</label>
                        <textarea class="form-control" name="contacts[${contactIndex}][note]"></textarea>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm remove-contact"><i class="fas fa-trash"></i> Sil</button>
                </div>
            </div>`;
        $('#contact-container').append(html);
        contactIndex++;
    });

    $(document).on('click', '.remove-address', function() {
        $(this).closest('.address-card').remove();
    });

    $(document).on('click', '.remove-contact', function() {
        $(this).closest('.contact-card').remove();
    });
});
</script>