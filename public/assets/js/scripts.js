/* Özel JS kodları */
$(document).ready(function() {
    // DataTables başlatma örneği
    $('.data-table').DataTable({
        responsive: true,
        paging: true,
        searching: true,
        ordering: true,
        dom: 'Bfrtip', // Buttons için gerekli
        buttons: [
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Yazdır',
                title: document.title,
                customize: function(win) {
                    $(win.document.body).find('.card-tools').remove(); // Butonları gizle
                    $(win.document.body).find('form').remove(); // Filtre formunu gizle
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                title: document.title
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                title: document.title
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                title: document.title
            }
        ]
    });

    // Sidebar ve diğer AdminLTE bileşenlerini başlatma
    $('[data-widget="treeview"]').Treeview('init');
});

// Genel yardımcı fonksiyonlar
function showAlert(message, type = 'success') {
    const alertHtml = `<div class="alert alert-${type} alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <i class="fas fa-info-circle"></i> ${message}
    </div>`;
    $('#alert-container').html(alertHtml);
}