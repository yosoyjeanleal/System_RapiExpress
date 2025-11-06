         <?php if (isset($_SESSION['mensaje']) && isset($_SESSION['tipo_mensaje'])): ?>
<script>
    Swal.fire({
        icon: '<?php echo $_SESSION["tipo_mensaje"] === "success" ? "success" : "error"; ?>',
        title: '<?php echo $_SESSION["tipo_mensaje"] === "success" ? "¡Éxito!" : "¡Error!"; ?>',
        text: '<?php echo $_SESSION["mensaje"]; ?>',
        timer: 3000,
        showConfirmButton: false
    });
</script>
<?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); endif; ?>