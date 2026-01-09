<?php
$titulo = "Inicio de Sesión";
include __DIR__ . '/../plantillas/header.php';

use App\Controladores\ControladorUsuario;
use App\Helpers\Security;
use App\Helpers\Validator;
use App\Helpers\Response;

// Variables para almacenar mensajes de error
$error = "";

// Procesar el formulario cuando se envíe
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = Security::sanitizeArray($_POST);

    $validator = new Validator($datos);
    $validator
        ->requerido('correo', 'El correo es obligatorio')
        ->email('correo', 'El correo no es válido')
        ->requerido('contraseña', 'La contraseña es obligatoria')
        ->min('contraseña', 6, 'La contraseña debe tener al menos 6 caracteres');

    if ($validator->pasa()) {
        $controlador = new ControladorUsuario();
        $resultado = $controlador->login($datos['correo'], $datos['contraseña']);

        if ($resultado['exito'] === true) {
            // Redirigir según rol
            $esAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
            $destino = $esAdmin ? $base_url . '/vistas/administracion/admin_dashboard.php' : $base_url . '/public/index.php';
            Response::redirect($destino);
        } else {
            $error = $resultado['mensaje'] ?? 'Credenciales inválidas';
        }
    } else {
        $error = $validator->primerError() ?? 'Datos inválidos';
    }
}
?>

<div class="container mt-2 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="col-md-3 col-lg-5">
        <h1 class="text-center mb-4">Inicio de Sesión</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo Security::escape($error); ?></div>
        <?php endif; ?>
        <form action="<?php echo Security::escape($_SERVER['PHP_SELF']); ?>" method="post">
            <div class="mb-3">
                <label for="correo" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control form-control-sm" id="correo" name="correo" required>
            </div>
            <div class="mb-3">
                <label for="contraseña" class="form-label">Contraseña</label>
                <input type="password" class="form-control form-control-sm" id="contraseña" name="contraseña" required>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-sm">Iniciar Sesión</button>
            </div>
            <div class="text-center mt-3">
                <a href="recuperar_contraseña.php" class="text-decoration-none">¿Olvidaste tu contraseña?</a>
            </div>
        </form>
    </div>
</div>

<?php
include __DIR__ . '/../plantillas/footer.php';
?>
