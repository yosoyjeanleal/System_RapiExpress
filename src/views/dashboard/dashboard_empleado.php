<!DOCTYPE html>
<html>

<head>
	<!-- Basic Page Info -->
	<meta charset="utf-8" />
	<title>RapiExpress - Dashboard</title>
	<link rel="icon" href="assets\img\logo-rapi.ico" type="image/x-icon">
	<!-- Mobile Specific Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
</head>

<body>
	<?php include 'src\views\partels\barras.php'; ?>
	<div class="main-container">
		<div class="xs-pd-20-10 pd-ltr-20">
			<div class="title pb-20">
				<h2 class="h3 mb-0">Dashboard</h2>
			</div>

			<div class="row pb-10">
				<div class="col-xl-3 col-lg-3 col-md-6 mb-20">
					<div class="card-box height-100-p widget-style3">
						<div class="d-flex flex-wrap">
							<div class="widget-data">
								<div class="weight-700 font-24 text-dark"><?= $totalClientes ?></div>
								<div class="font-14 text-secondary weight-500">
									Clientes
								</div>
							</div>
							<div class="widget-icon">
								<div class="icon" data-color="#00eccf">
									<i class="micon bi bi-people-fill"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-lg-3 col-md-6 mb-20">
					<div class="card-box height-100-p widget-style3">
						<div class="d-flex flex-wrap">
							<div class="widget-data">
								<div class="weight-700 font-24 text-dark"><?= $totalUsuarios ?></div>
								<div class="font-14 text-secondary weight-500">
									Empleados
								</div>
							</div>
							<div class="widget-icon">
								<div class="icon" data-color="#ff5b5b">
									<span class="micon bi bi-person-square"></span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-xl-3 col-lg-3 col-md-6 mb-20">
					<div class="card-box height-100-p widget-style3">
						<div class="d-flex flex-wrap">
							<div class="widget-data">
								<div class="weight-700 font-24 text-dark">000</div>
								<div class="font-14 text-secondary weight-500">
									Entregas
								</div>
							</div>
							<div class="widget-icon">
								<div class="icon">
									<i
										class="icon-copy bi bi-box-arrow-up"
										aria-hidden="true"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-lg-3 col-md-6 mb-20">
					<div class="card-box height-100-p widget-style3">
						<div class="d-flex flex-wrap">
							<div class="widget-data">
								<div class="weight-700 font-24 text-dark">00</div>
								<div class="font-14 text-secondary weight-500">Fallidos</div>
							</div>
							<div class="widget-icon">
								<div class="icon" data-color="#09cc06">
									<i class="icon-copy bi bi-x-octagon" aria-hidden="true"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row pb-10">
				<div class="col-xl-3 col-lg-3 col-md-6 mb-20">
					<div class="card-box height-100-p widget-style3">
						<div class="d-flex flex-wrap">
							<div class="widget-data">
								<div class="weight-700 font-24 text-dark"><?= $totalTiendas ?? '0' ?></div>
								<div class="font-14 text-secondary weight-500">Tiendas</div>
							</div>
							<div class="widget-icon">
								<div class="icon" data-color="#ff9f00">
									<i class="micon bi bi-shop-window"></i>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-xl-3 col-lg-3 col-md-6 mb-20">
					<div class="card-box height-100-p widget-style3">
						<div class="d-flex flex-wrap">
							<div class="widget-data">
								<div class="weight-700 font-24 text-dark"><?= $totalCouriers ?? '0' ?></div>
								<div class="font-14 text-secondary weight-500">Couriers</div>
							</div>
							<div class="widget-icon">
								<div class="icon" data-color="#3b7ddd">
									<i class="micon bi bi-truck"></i>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-xl-3 col-lg-3 col-md-6 mb-20">
					<div class="card-box height-100-p widget-style3">
						<div class="d-flex flex-wrap">
							<div class="widget-data">
								<div class="weight-700 font-24 text-dark"><?= $totalPaquetes ?? '0' ?></div>
								<div class="font-14 text-secondary weight-500">Paquetes</div>
							</div>
							<div class="widget-icon">
								<div class="icon" data-color="#6c757d">
									<i class="micon bi bi-box-seam"></i>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-xl-3 col-lg-3 col-md-6 mb-20">
					<div class="card-box height-100-p widget-style3">
						<div class="d-flex flex-wrap">
							<div class="widget-data">
								<div class="weight-700 font-24 text-dark"><?= $totalReportes ?? '0' ?></div>
								<div class="font-14 text-secondary weight-500">Reportes</div>
							</div>
							<div class="widget-icon">
								<div class="icon" data-color="#dc3545">
									<i class="micon bi bi-bar-chart-line-fill"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>


			<div class="card-box mb-30">
				<div class="pd-30">
					<h4 class="text-blue h4">Lista de Clientes</h4>
				</div>
				<div class="pb-30">
					<table class="data-table table stripe hover nowrap" id="clientesTable">
						<thead>
							<tr>
								<th>Cédula</th>
								<th>Sucursal</th>
								<th>Casillero</th>
								<th>Nombre y Apellido</th>
								<th>Dirección</th>
								<th>Teléfono</th>
								<th>Correo</th>
								<th>Fecha Registro</th>

							</tr>
						</thead>
						<tbody>
							<?php foreach ($clientes as $cliente): ?>
								<tr>
									<td><?= htmlspecialchars($cliente['Cedula_Identidad']) ?></td>
									<td><?= htmlspecialchars($cliente['Sucursal_Nombre'] ?? 'Sin sucursal') ?></td>
									<td><?= htmlspecialchars($cliente['Casillero_Nombre'] ?? 'Sin casillero') ?></td>
									<td class="table-plus"><?= htmlspecialchars($cliente['Nombres_Cliente'] . ' ' . $cliente['Apellidos_Cliente']) ?></td>
									<td><?= htmlspecialchars($cliente['Direccion_Cliente']) ?></td>
									<td><?= htmlspecialchars($cliente['Telefono_Cliente']) ?></td>
									<td><?= htmlspecialchars($cliente['Correo_Cliente']) ?></td>
									<td><?= date('d/m/Y', strtotime($cliente['Fecha_Registro'])) ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<div class="footer-wrap pd-20 mb-20 card-box">
					RapiExpress © 2025 - Sistema de Gestión de Paquetes
				</div>
			</div>
		</div>


</body>

</html>