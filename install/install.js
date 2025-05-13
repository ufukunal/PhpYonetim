$(document).ready(function() {
    $('form').on('submit', function(e) {
        let valid = true;
        $(this).find('input[required], select[required]').each(function() {
            if (!$(this).val()) {
                valid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        if (!valid) {
            e.preventDefault();
            alert('Lütfen tüm zorunlu alanları doldurun.');
        }
    });
});