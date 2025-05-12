/* Özel JS kodları */
$(document).ready(function() {
    // DataTables başlatma örneği
    $('.data-table').DataTable({
        responsive: true,
        paging: true,
        searching: true,
        ordering: true
    });

    // Sidebar ve diğer AdminLTE bileşenlerini başlatma
    $('[data-widget="treeview"]').Treeview('init');
});

// Genel yardımcı fonksiyonlar
function showAlert(message, type = 'success') {
    const alertHtml = `<div class="alert alert-${type} alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        ${message}
    </div>`;
    $('#alert-container').html(alertHtml);
}