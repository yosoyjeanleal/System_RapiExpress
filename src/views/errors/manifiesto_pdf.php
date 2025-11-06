<?php /** @var array $saca */ ?>
<?php /** @var array $paquetes */ ?>

<style>
    body { font-family: helvetica; font-size: 10pt; }
    h1, h2, h3 { text-align: center; margin: 0; }
    table { border-collapse: collapse; width: 100%; margin-top: 10px; }
    th, td { border: 1px solid #000; padding: 5px; text-align: left; font-size: 9pt; }
    th { background-color: #f2f2f2; }
    .info { margin-top: 10px; font-size: 10pt; }
</style>

<h2>MANIFIESTO DE SACA #<?= htmlspecialchars($saca['ID_Saca']) ?></h2>
<p class="info"><strong>Creado por:</strong> <?= htmlspecialchars($saca['Nombres_Usuario'].' '.$saca['Apellidos_Usuario']) ?></p>
<p class="info"><strong>Fecha:</strong> <?= date('d/m/Y H:i') ?></p>
<p class="info"><strong>Peso Total:</strong> <?= number_format($saca['Peso_Total'], 2) ?> kg</p>

<hr>

<h3>Contenido de la Saca</h3>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Tracking</th>
            <th>Cliente</th>
            <th>Origen (Tienda)</th>
            <th>Destino (Sucursal)</th>
            <th>Categoría</th>
            <th>Descripción</th>
            <th>Peso (kg)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($paquetes as $index => $p): ?>
        <tr>
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($p['Tracking']) ?></td>
            <td><?= htmlspecialchars($p['Nombres_Cliente'].' '.$p['Apellidos_Cliente']) ?></td>
            <td><?= htmlspecialchars($p['Origen'] ?? '-') ?></td>
            <td><?= htmlspecialchars($p['Destino'] ?? '-') ?></td>
            <td><?= htmlspecialchars($p['Categoria'] ?? '-') ?></td>
            <td><?= htmlspecialchars($p['Descripcion'] ?? '-') ?></td>
            <td><?= number_format($p['Paquete_Peso'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<p class="info" style="margin-top:20px; text-align:center;">
    _______________________________<br>
    Firma Responsable
</p>
