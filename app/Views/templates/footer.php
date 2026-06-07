</div>
<!-- ./wrapper -->

<style>
/* UI polish: consistent table spacing and pagination */
.table td, .table th { vertical-align: middle; }
.table thead th { background: #f8f9fa; }
.table .badge { font-size: 0.9rem; }
.page-link { color: #dc3545; }
.pagination .active .page-link { background-color: #dc3545; border-color: #dc3545; }
.datatable_wrapper .dataTables_paginate .paginate_button { padding: .25rem .5rem; }
@media (max-width: 768px) {
    .card-title { font-size: 1rem; }
    .small-box .inner h3 { font-size: 1.2rem; }
}
</style>

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/js/adminlte.min.js"></script>
<!-- DataTables -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        // Created by rikisetiyopambudi@gmail.com
        // Inisialisasi DataTable
        $('.datatable').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });

        // Auto-hide flash messages setelah 5 detik
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
</body>
</html>