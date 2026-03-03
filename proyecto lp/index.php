 
 <?php
$host = 'db'; $db = 'asistencia_db'; $user = 'user_p3'; $pass = 'password123';
try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) { die("Error: " . $e->getMessage()); }

# Crear tablas si no existen (PostgreSQL)
$pdo->exec("CREATE TABLE IF NOT EXISTS asistentes (
    id SERIAL PRIMARY KEY,
    nombre_estudiante TEXT NOT NULL,
    carrera TEXT NOT NULL,
    asistencia_confirmada BOOLEAN DEFAULT FALSE,
    fecha_registro TIMESTAMP NOT NULL DEFAULT now()
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS portfolio (
    id SERIAL PRIMARY KEY,
    nombre TEXT NOT NULL,
    foto TEXT,
    bio TEXT,
    habilidades TEXT,
    fecha_registro TIMESTAMP NOT NULL DEFAULT now()
)");

// Asegurar carpeta uploads
if (!is_dir(__DIR__ . '/uploads')) {
    @mkdir(__DIR__ . '/uploads', 0755, true);
}

# INSERT: Agregar asistente (asegurar fecha_registro)
if (isset($_POST['agregar'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $carrera = trim($_POST['carrera'] ?? '');
    if ($nombre !== '' && $carrera !== '') {
        $stmt = $pdo->prepare("INSERT INTO asistentes (nombre_estudiante, carrera, fecha_registro) VALUES (?, ?, now())");
        $stmt->execute([$nombre, $carrera]);
        header('Location: '.$_SERVER['PHP_SELF']); exit;
    }
}

# UPDATE: Editar nombre y carrera del estudiante
if (isset($_POST['editar']) && !empty($_POST['id'])) {
    $id = (int)$_POST['id'];
    $nombre = trim($_POST['nombre'] ?? '');
    $carrera = trim($_POST['carrera'] ?? '');
    if ($nombre !== '' && $carrera !== '') {
        $stmt = $pdo->prepare("UPDATE asistentes SET nombre_estudiante = ?, carrera = ? WHERE id = ?");
        $stmt->execute([$nombre, $carrera, $id]);
        header('Location: '.$_SERVER['PHP_SELF']); exit;
    }
}

# TOGGLE asistencia
if (isset($_GET['toggle'])) {
    $stmt = $pdo->prepare("UPDATE asistentes SET asistencia_confirmada = NOT asistencia_confirmada WHERE id = ?");
    $stmt->execute([(int)$_GET['toggle']]);
    header('Location: '.$_SERVER['PHP_SELF']); exit;
}

# DELETE: Eliminar registro
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM asistentes WHERE id = ?");
    $stmt->execute([(int)$_GET['delete']]);
    header('Location: '.$_SERVER['PHP_SELF']); exit;
}

# PORTFOLIO: Añadir entrada (subida de foto opcional)
if (isset($_POST['add_portfolio'])) {
    $nombre = trim($_POST['p_nombre'] ?? '');
    $bio = trim($_POST['p_bio'] ?? '');
    $habilidades = trim($_POST['p_habilidades'] ?? '');
    $fotoPath = null;
    if (!empty($_FILES['p_foto']['name']) && $_FILES['p_foto']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['p_foto']['tmp_name'];
        $orig = basename($_FILES['p_foto']['name']);
        $ext = pathinfo($orig, PATHINFO_EXTENSION);
        $name = uniqid('foto_') . ($ext ? '.' . $ext : '');
        $dest = __DIR__ . '/uploads/' . $name;
        if (move_uploaded_file($tmp, $dest)) {
            $fotoPath = 'uploads/' . $name;
        }
    }
    if ($nombre !== '') {
        $stmt = $pdo->prepare("INSERT INTO portfolio (nombre, foto, bio, habilidades, fecha_registro) VALUES (?, ?, ?, ?, now())");
        $stmt->execute([$nombre, $fotoPath, $bio, $habilidades]);
        header('Location: '.$_SERVER['PHP_SELF']); exit;
    }
}

# Obtener datos
$asistentes = $pdo->query("SELECT * FROM asistentes ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$portfolio = $pdo->query("SELECT * FROM portfolio ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html><head><meta charset="utf-8"><title>Portafolio y Asistentes</title>
<style>body{font-family:Arial,Helvetica,sans-serif;margin:20px}img.foto{max-width:150px;height:auto;border-radius:6px}table{border-collapse:collapse;margin-top:10px}table,th,td{border:1px solid #ccc;padding:6px}</style>
</head><body>

<h1>Portafolio Profesional</h1>
<section>si s
    <h2>Agregar / Editar Portafolio</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="p_nombre" placeholder="Nombre" required>
        <input type="file" name="p_foto" accept="image/*">
        <br>
        <textarea name="p_bio" placeholder="Bio"></textarea>
        <input type="text" name="p_habilidades" placeholder="Habilidades (separadas por comas)">
        <button type="submit" name="add_portfolio">Agregar Portafolio</button>
    </form>

    <h3>Entradas</h3>
    <?php foreach($portfolio as $p): ?>
        <div style="margin-bottom:18px">
            <strong><?= htmlspecialchars($p['nombre']) ?></strong> — <small><?= htmlspecialchars($p['fecha_registro']) ?></small><br>
            <?php if (!empty($p['foto'])): ?>
                <img src="<?= htmlspecialchars($p['foto']) ?>" class="foto" alt="foto">
            <?php endif; ?>
            <p><?= nl2br(htmlspecialchars($p['bio'])) ?></p>
            <p><em>Habilidades:</em> <?= htmlspecialchars($p['habilidades']) ?></p>
        </div>
    <?php endforeach; ?>
</section>

<hr>
<h2>Registro de Asistentes</h2>

<?php if (isset($_GET['edit'])):
    $editId = (int)$_GET['edit'];
    $row = null;
    foreach ($asistentes as $aa) if ($aa['id'] == $editId) { $row = $aa; break; }
    if ($row): ?>
    <h3>Editar Estudiante #<?= $editId ?></h3>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $editId ?>">
        <input type="text" name="nombre" value="<?= htmlspecialchars($row['nombre_estudiante']) ?>" required>
        <input type="text" name="carrera" value="<?= htmlspecialchars($row['carrera']) ?>" required>
        <button type="submit" name="editar">Guardar Cambios</button>
        <a href="<?= $_SERVER['PHP_SELF'] ?>">Cancelar</a>
    </form>
    <?php endif; endif; ?>

<h3>Registrar Asistencia</h3>
<form method="POST">
    <input type="text" name="nombre" placeholder="Nombre Estudiante" required>
    <input type="text" name="carrera" placeholder="Carrera" required>
    <button type="submit" name="agregar">Registrar Asistencia</button>
</form>

<table>
    <tr>
        <th>Nombre</th><th>Carrera</th><th>Estado</th><th>Fecha Registro</th><th>Acciones</th>
    </tr>
    <?php foreach($asistentes as $a): ?>
    <tr>
        <td><?= htmlspecialchars($a['nombre_estudiante']) ?></td>
        <td><?= htmlspecialchars($a['carrera']) ?></td>
        <td><?= $a['asistencia_confirmada'] ? 'Asistió' : 'Pendiente' ?></td>
        <td><?= htmlspecialchars($a['fecha_registro']) ?></td>
        <td>
            <a href="?toggle=<?= $a['id'] ?>">Cambiar Estado</a> |
            <a href="?edit=<?= $a['id'] ?>">Editar</a> |
            <a href="?delete=<?= $a['id'] ?>" onclick="return confirm('Eliminar?')">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<h3>Reporte del Sistema:</h3>
<pre><?php echo @file_get_contents('reports/reporte.txt') ?: 'Sin reporte disponible.'; ?></pre>

</body></html>
