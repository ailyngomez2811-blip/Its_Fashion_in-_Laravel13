<?php
// Evitar que Blade intente compilar etiquetas con prefijo x: u o:
echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
    xmlns:o="urn:schemas-microsoft-com:office:office"
    xmlns:x="urn:schemas-microsoft-com:office:excel"
    xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
    xmlns:html="http://www.w3.org/TR/REC-html40">
    <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
        <Author><?php echo htmlspecialchars((Auth::user()?->nombre ?? 'Admin') . ' ' . (Auth::user()?->apellido ?? 'General')); ?></Author>
        <Created><?php echo now()->toIso8601String(); ?></Created>
        <Version>16.00</Version>
    </DocumentProperties>
    <OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office">
        <AllowPNG />
    </OfficeDocumentSettings>
    <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
        <WindowHeight>10000</WindowHeight>
        <WindowWidth>20000</WindowWidth>
        <WindowTopX>0</WindowTopX>
        <WindowTopY>0</WindowTopY>
        <ProtectStructure>False</ProtectStructure>
        <ProtectWindows>False</ProtectWindows>
    </ExcelWorkbook>
    <Styles>
        <Style ss:ID="Default" ss:Name="Normal">
            <Alignment ss:Vertical="Bottom" /><Borders/><Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000" /><Interior/><NumberFormat/><Protection/>
        </Style>
        <!-- Titulos -->
        <Style ss:ID="sTitle">
            <Alignment ss:Vertical="Bottom" /><Font ss:FontName="Calibri" ss:Size="22" ss:Bold="1" ss:Color="#2563eb" />
        </Style>
        <Style ss:ID="sSubtitle">
            <Alignment ss:Vertical="Center" /><Font ss:FontName="Calibri" ss:Size="14" ss:Bold="1" ss:Color="#1e293b" />
        </Style>
        <Style ss:ID="sMeta">
            <Alignment ss:Vertical="Top" /><Font ss:FontName="Calibri" ss:Size="11" ss:Color="#475569" />
        </Style>
        <Style ss:ID="sMetaRight">
            <Alignment ss:Horizontal="Right" ss:Vertical="Center" /><Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#475569" />
        </Style>
        <Style ss:ID="sSectionHeader">
            <Alignment ss:Vertical="Bottom" /><Font ss:FontName="Calibri" ss:Size="13" ss:Bold="1" ss:Color="#2563eb" /><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#475569" /></Borders>
        </Style>
        <!-- KPI Styles -->
        <Style ss:ID="sKpiValue">
            <Alignment ss:Horizontal="Center" ss:Vertical="Center" /><Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#475569" /></Borders><Font ss:FontName="Calibri" ss:Size="14" ss:Bold="1" ss:Color="#1e3a8a" /><Interior ss:Color="#f8fafc" ss:Pattern="Solid" />
        </Style>
        <Style ss:ID="sKpiTitle">
            <Alignment ss:Horizontal="Center" ss:Vertical="Center" /><Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#475569" /></Borders><Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#475569" /><Interior ss:Color="#f8fafc" ss:Pattern="Solid" />
        </Style>
        <!-- Table Header -->
        <Style ss:ID="sTableHeader">
            <Alignment ss:Vertical="Center" /><Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#475569" /></Borders><Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#000000" /><Interior ss:Color="#f1f5f9" ss:Pattern="Solid" />
        </Style>
        <Style ss:ID="sTableHeaderRight">
            <Alignment ss:Horizontal="Right" ss:Vertical="Center" /><Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#475569" /></Borders><Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#000000" /><Interior ss:Color="#f1f5f9" ss:Pattern="Solid" />
        </Style>
        <Style ss:ID="sTableHeaderCenter">
            <Alignment ss:Horizontal="Center" ss:Vertical="Center" /><Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#475569" /></Borders><Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#000000" /><Interior ss:Color="#f1f5f9" ss:Pattern="Solid" />
        </Style>
        <!-- Data Rows -->
        <Style ss:ID="sDataLeft">
            <Alignment ss:Vertical="Center" /><Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#475569" /></Borders><Font ss:FontName="Calibri" ss:Size="11" />
        </Style>
        <Style ss:ID="sDataLeftAlt">
            <Alignment ss:Vertical="Center" /><Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#475569" /></Borders><Font ss:FontName="Calibri" ss:Size="11" /><Interior ss:Color="#f8fafc" ss:Pattern="Solid" />
        </Style>
        <Style ss:ID="sDataLeftBold">
            <Alignment ss:Vertical="Center" /><Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#475569" /></Borders><Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" />
        </Style>
        <Style ss:ID="sDataLeftBoldAlt">
            <Alignment ss:Vertical="Center" /><Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#475569" /></Borders><Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" /><Interior ss:Color="#f8fafc" ss:Pattern="Solid" />
        </Style>
        <Style ss:ID="sDataRight">
            <Alignment ss:Horizontal="Right" ss:Vertical="Center" /><Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#475569" /></Borders><Font ss:FontName="Calibri" ss:Size="11" />
        </Style>
        <Style ss:ID="sDataRightAlt">
            <Alignment ss:Horizontal="Right" ss:Vertical="Center" /><Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#475569" /></Borders><Font ss:FontName="Calibri" ss:Size="11" /><Interior ss:Color="#f8fafc" ss:Pattern="Solid" />
        </Style>
        <Style ss:ID="sDataRightBold">
            <Alignment ss:Horizontal="Right" ss:Vertical="Center" /><Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#475569" /></Borders><Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" />
        </Style>
        <Style ss:ID="sDataRightBoldAlt">
            <Alignment ss:Horizontal="Right" ss:Vertical="Center" /><Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#475569" /></Borders><Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" /><Interior ss:Color="#f8fafc" ss:Pattern="Solid" />
        </Style>
        <Style ss:ID="sDataCenter">
            <Alignment ss:Horizontal="Center" ss:Vertical="Center" /><Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#475569" /></Borders><Font ss:FontName="Calibri" ss:Size="11" />
        </Style>
        <Style ss:ID="sDataCenterAlt">
            <Alignment ss:Horizontal="Center" ss:Vertical="Center" /><Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#475569" /></Borders><Font ss:FontName="Calibri" ss:Size="11" /><Interior ss:Color="#f8fafc" ss:Pattern="Solid" />
        </Style>
        <!-- Statuses -->
        <Style ss:ID="sStatusGreen">
            <Alignment ss:Horizontal="Center" ss:Vertical="Center" /><Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#475569" /></Borders><Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#16a34a" />
        </Style>
        <Style ss:ID="sStatusGreenAlt">
            <Alignment ss:Horizontal="Center" ss:Vertical="Center" /><Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#475569" /></Borders><Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#16a34a" /><Interior ss:Color="#f8fafc" ss:Pattern="Solid" />
        </Style>
        <Style ss:ID="sStatusRed">
            <Alignment ss:Horizontal="Center" ss:Vertical="Center" /><Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#475569" /></Borders><Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#b91c1c" />
        </Style>
        <Style ss:ID="sStatusRedAlt">
            <Alignment ss:Horizontal="Center" ss:Vertical="Center" /><Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#475569" /></Borders><Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#b91c1c" /><Interior ss:Color="#f8fafc" ss:Pattern="Solid" />
        </Style>
        <!-- Totals -->
        <Style ss:ID="sTotalLabel">
            <Alignment ss:Horizontal="Right" ss:Vertical="Center" /><Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#475569" /></Borders><Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#000000" /><Interior ss:Color="#f8fafc" ss:Pattern="Solid" />
        </Style>
        <Style ss:ID="sTotalValue">
            <Alignment ss:Horizontal="Right" ss:Vertical="Center" /><Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#475569" /></Borders><Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#2563eb" /><Interior ss:Color="#f8fafc" ss:Pattern="Solid" />
        </Style>
    </Styles>

    <?php if ($incluirVentas): ?>
        <Worksheet ss:Name="Ventas e Ingresos">
            <Table ss:ExpandedColumnCount="6" x:FullColumns="1" x:FullRows="1" ss:DefaultRowHeight="22">
                <Column ss:Width="110" />
                <Column ss:Width="160" />
                <Column ss:Width="200" />
                <Column ss:Width="160" />
                <Column ss:Width="120" />
                <Column ss:Width="140" />

                <Row ss:Height="40">
                    <Cell ss:MergeAcross="3" ss:StyleID="sTitle"><Data ss:Type="String">Its Fashion</Data></Cell>
                    <Cell ss:MergeAcross="1" ss:StyleID="sMetaRight"><Data ss:Type="String">Generado por: <?php echo htmlspecialchars((Auth::user()?->nombre ?? 'Admin') . ' ' . (Auth::user()?->apellido ?? 'General')); ?></Data></Cell>
                </Row>
                <Row ss:Height="25">
                    <Cell ss:MergeAcross="3" ss:StyleID="sSubtitle"><Data ss:Type="String">Consolidado de Actividad Comercial - Ventas</Data></Cell>
                    <Cell ss:MergeAcross="1" ss:StyleID="sMetaRight"><Data ss:Type="String">Fecha: <?php echo now()->format('d/m/Y H:i'); ?></Data></Cell>
                </Row>
                <Row ss:Height="20">
                    <Cell ss:MergeAcross="5" ss:StyleID="sMeta"><Data ss:Type="String">Rango de evaluación: <?php echo date('d/m/Y', strtotime($desde)); ?> al <?php echo date('d/m/Y', strtotime($hasta)); ?></Data></Cell>
                </Row>
                <Row ss:Height="15" />
                <Row ss:Height="30">
                    <Cell ss:MergeAcross="5" ss:StyleID="sSectionHeader"><Data ss:Type="String">Resumen e Ingresos del Periodo</Data></Cell>
                </Row>
                <Row ss:Height="10" />

                <!-- KPIs -->
                <Row ss:Height="22">
                    <Cell ss:StyleID="sKpiTitle"><Data ss:Type="String">Total Ingresos</Data></Cell>
                    <Cell ss:StyleID="sKpiTitle"><Data ss:Type="String">Ventas</Data></Cell>
                    <Cell ss:MergeAcross="1" ss:StyleID="sKpiTitle"><Data ss:Type="String">Ticket Promedio</Data></Cell>
                    <Cell ss:MergeAcross="1" ss:StyleID="sKpiTitle"><Data ss:Type="String">Efectivo</Data></Cell>
                </Row>
                <Row ss:Height="28">
                    <Cell ss:StyleID="sKpiValue"><Data ss:Type="String">$<?php echo number_format($kpi->total_periodo ?? 0, 2); ?></Data></Cell>
                    <Cell ss:StyleID="sKpiValue"><Data ss:Type="String"><?php echo $kpi->transacciones ?? 0; ?></Data></Cell>
                    <Cell ss:MergeAcross="1" ss:StyleID="sKpiValue"><Data ss:Type="String">$<?php echo number_format($kpi->ticket_promedio ?? 0, 2); ?></Data></Cell>
                    <Cell ss:MergeAcross="1" ss:StyleID="sKpiValue"><Data ss:Type="String">$<?php echo number_format($kpi->efectivo ?? 0, 2); ?></Data></Cell>
                </Row>
                <Row ss:Height="15" />

                <!-- Headers -->
                <Row ss:Height="26">
                    <Cell ss:StyleID="sTableHeader"><Data ss:Type="String">ID Venta</Data></Cell>
                    <Cell ss:StyleID="sTableHeader"><Data ss:Type="String">Fecha</Data></Cell>
                    <Cell ss:StyleID="sTableHeader"><Data ss:Type="String">Cliente</Data></Cell>
                    <Cell ss:StyleID="sTableHeader"><Data ss:Type="String">Método Pago</Data></Cell>
                    <Cell ss:StyleID="sTableHeaderCenter"><Data ss:Type="String">Estado</Data></Cell>
                    <Cell ss:StyleID="sTableHeaderRight"><Data ss:Type="String">Total</Data></Cell>
                </Row>

                <!-- Data -->
                <?php foreach ($ventas as $idx => $v): ?>
                    <?php
                    $alt = ($idx % 2 === 0) ? '' : 'Alt';
                    $statusStyle = ($v->estado === 'Completada') ? 'sStatusGreen' . $alt : 'sStatusRed' . $alt;
                    ?>
                    <Row ss:Height="24">
                        <Cell ss:StyleID="sDataLeftBold<?php echo $alt; ?>"><Data ss:Type="String">#<?php echo str_pad($v->id, 5, '0', STR_PAD_LEFT); ?></Data></Cell>
                        <Cell ss:StyleID="sDataLeft<?php echo $alt; ?>"><Data ss:Type="String"><?php echo $v->fecha ? $v->fecha->format('d/m/Y H:i') : '—'; ?></Data></Cell>
                        <Cell ss:StyleID="sDataLeftBold<?php echo $alt; ?>"><Data ss:Type="String"><?php echo htmlspecialchars(($v->cliente?->nombre ?? 'Venta Mostrador') . ' ' . ($v->cliente?->apellido ?? '')); ?></Data></Cell>
                        <Cell ss:StyleID="sDataLeft<?php echo $alt; ?>"><Data ss:Type="String"><?php echo htmlspecialchars($v->metodo_pago); ?></Data></Cell>
                        <Cell ss:StyleID="<?php echo $statusStyle; ?>"><Data ss:Type="String"><?php echo htmlspecialchars($v->estado); ?></Data></Cell>
                        <Cell ss:StyleID="sDataRightBold<?php echo $alt; ?>"><Data ss:Type="String">$<?php echo number_format($v->total, 2); ?></Data></Cell>
                    </Row>
                <?php endforeach; ?>

                <?php if ($ventas->isEmpty()): ?>
                    <Row ss:Height="24">
                        <Cell ss:MergeAcross="5" ss:StyleID="sDataCenter"><Data ss:Type="String">No se registraron ventas en este período.</Data></Cell>
                    </Row>
                <?php endif; ?>

                <Row ss:Height="28">
                    <Cell ss:MergeAcross="4" ss:StyleID="sTotalLabel"><Data ss:Type="String">TOTAL ACUMULADO:</Data></Cell>
                    <Cell ss:StyleID="sTotalValue"><Data ss:Type="String">$<?php echo number_format($kpi->total_periodo ?? 0, 2); ?></Data></Cell>
                </Row>
            </Table>
        </Worksheet>
    <?php endif; ?>

    <?php if ($incluirProductos): ?>
        <Worksheet ss:Name="Prendas Mas Vendidas">
            <Table ss:ExpandedColumnCount="6" x:FullColumns="1" x:FullRows="1" ss:DefaultRowHeight="22">
                <Column ss:Width="90" />
                <Column ss:Width="260" />
                <Column ss:Width="120" />
                <Column ss:Width="120" />
                <Column ss:Width="120" />
                <Column ss:Width="150" />

                <Row ss:Height="40">
                    <Cell ss:MergeAcross="3" ss:StyleID="sTitle"><Data ss:Type="String">Its Fashion</Data></Cell>
                    <Cell ss:MergeAcross="1" ss:StyleID="sMetaRight"><Data ss:Type="String">Generado por: <?php echo htmlspecialchars((Auth::user()?->nombre ?? 'Admin') . ' ' . (Auth::user()?->apellido ?? 'General')); ?></Data></Cell>
                </Row>
                <Row ss:Height="25">
                    <Cell ss:MergeAcross="3" ss:StyleID="sSubtitle"><Data ss:Type="String">Ranking de Prendas Más Vendidas</Data></Cell>
                    <Cell ss:MergeAcross="1" ss:StyleID="sMetaRight"><Data ss:Type="String">Fecha: <?php echo now()->format('d/m/Y H:i'); ?></Data></Cell>
                </Row>
                <Row ss:Height="20">
                    <Cell ss:MergeAcross="5" ss:StyleID="sMeta"><Data ss:Type="String">Rango de evaluación: <?php echo date('d/m/Y', strtotime($desde)); ?> al <?php echo date('d/m/Y', strtotime($hasta)); ?></Data></Cell>
                </Row>
                <Row ss:Height="15" />
                <Row ss:Height="30">
                    <Cell ss:MergeAcross="5" ss:StyleID="sSectionHeader"><Data ss:Type="String">Prendas con Mayor Rotación</Data></Cell>
                </Row>
                <Row ss:Height="10" />

                <!-- Headers -->
                <Row ss:Height="26">
                    <Cell ss:StyleID="sTableHeaderCenter"><Data ss:Type="String">Posición</Data></Cell>
                    <Cell ss:MergeAcross="1" ss:StyleID="sTableHeader"><Data ss:Type="String">Producto / Prenda</Data></Cell>
                    <Cell ss:StyleID="sTableHeader"><Data ss:Type="String">Talla</Data></Cell>
                    <Cell ss:StyleID="sTableHeader"><Data ss:Type="String">Color</Data></Cell>
                    <Cell ss:StyleID="sTableHeaderRight"><Data ss:Type="String">Uds. Vendidas</Data></Cell>
                </Row>

                <!-- Data -->
                <?php foreach ($productos as $idx => $p): ?>
                    <?php $alt = ($idx % 2 === 0) ? '' : 'Alt'; ?>
                    <Row ss:Height="24">
                        <Cell ss:StyleID="sDataCenter<?php echo $alt; ?>"><Data ss:Type="String"><?php echo $idx + 1; ?></Data></Cell>
                        <Cell ss:MergeAcross="1" ss:StyleID="sDataLeftBold<?php echo $alt; ?>"><Data ss:Type="String"><?php echo htmlspecialchars($p->nombre); ?></Data></Cell>
                        <Cell ss:StyleID="sDataLeft<?php echo $alt; ?>"><Data ss:Type="String"><?php echo htmlspecialchars($p->talla); ?></Data></Cell>
                        <Cell ss:StyleID="sDataLeft<?php echo $alt; ?>"><Data ss:Type="String"><?php echo htmlspecialchars($p->color); ?></Data></Cell>
                        <Cell ss:StyleID="sDataRightBold<?php echo $alt; ?>"><Data ss:Type="String"><?php echo $p->vendidos; ?> uds</Data></Cell>
                    </Row>
                <?php endforeach; ?>

                <?php if ($productos->isEmpty()): ?>
                    <Row ss:Height="24">
                        <Cell ss:MergeAcross="5" ss:StyleID="sDataCenter"><Data ss:Type="String">Sin datos de transacciones de prendas.</Data></Cell>
                    </Row>
                <?php endif; ?>
            </Table>
        </Worksheet>
    <?php endif; ?>

    <?php if ($incluirInventario): ?>
        <Worksheet ss:Name="Inventario General">
            <Table ss:ExpandedColumnCount="6" x:FullColumns="1" x:FullRows="1" ss:DefaultRowHeight="22">
                <Column ss:Width="260" />
                <Column ss:Width="140" />
                <Column ss:Width="120" />
                <Column ss:Width="120" />
                <Column ss:Width="120" />
                <Column ss:Width="150" />

                <Row ss:Height="40">
                    <Cell ss:MergeAcross="3" ss:StyleID="sTitle"><Data ss:Type="String">Its Fashion</Data></Cell>
                    <Cell ss:MergeAcross="1" ss:StyleID="sMetaRight"><Data ss:Type="String">Generado por: <?php echo htmlspecialchars((Auth::user()?->nombre ?? 'Admin') . ' ' . (Auth::user()?->apellido ?? 'General')); ?></Data></Cell>
                </Row>
                <Row ss:Height="25">
                    <Cell ss:MergeAcross="3" ss:StyleID="sSubtitle"><Data ss:Type="String">Reporte General de Inventario</Data></Cell>
                    <Cell ss:MergeAcross="1" ss:StyleID="sMetaRight"><Data ss:Type="String">Fecha: <?php echo now()->format('d/m/Y H:i'); ?></Data></Cell>
                </Row>
                <Row ss:Height="20">
                    <Cell ss:MergeAcross="5" ss:StyleID="sMeta"><Data ss:Type="String">Catálogo de prendas activas y stock</Data></Cell>
                </Row>
                <Row ss:Height="15" />
                <Row ss:Height="30">
                    <Cell ss:MergeAcross="5" ss:StyleID="sSectionHeader"><Data ss:Type="String">Estado Físico del Stock</Data></Cell>
                </Row>
                <Row ss:Height="10" />

                <!-- Headers -->
                <Row ss:Height="26">
                    <Cell ss:MergeAcross="1" ss:StyleID="sTableHeader"><Data ss:Type="String">Producto</Data></Cell>
                    <Cell ss:StyleID="sTableHeader"><Data ss:Type="String">Categoría</Data></Cell>
                    <Cell ss:StyleID="sTableHeader"><Data ss:Type="String">Talla</Data></Cell>
                    <Cell ss:StyleID="sTableHeader"><Data ss:Type="String">Color</Data></Cell>
                    <Cell ss:StyleID="sTableHeaderRight"><Data ss:Type="String">Stock Disponible</Data></Cell>
                </Row>

                <!-- Data -->
                <?php foreach ($inventario as $idx => $r): ?>
                    <?php
                    $alt = ($idx % 2 === 0) ? '' : 'Alt';
                    $isCritical = ($r->stock == 0) || ($r->stock <= ($r->stock_minimo ?? 0));
                    $stockStyle = $isCritical ? 'sStatusRed' . $alt : 'sDataRightBold' . $alt;
                    ?>
                    <Row ss:Height="24">
                        <Cell ss:MergeAcross="1" ss:StyleID="sDataLeftBold<?php echo $alt; ?>"><Data ss:Type="String"><?php echo htmlspecialchars($r->nombre); ?></Data></Cell>
                        <Cell ss:StyleID="sDataLeft<?php echo $alt; ?>"><Data ss:Type="String"><?php echo htmlspecialchars($r->categoria?->nombre ?? '—'); ?></Data></Cell>
                        <Cell ss:StyleID="sDataLeft<?php echo $alt; ?>"><Data ss:Type="String"><?php echo htmlspecialchars($r->talla); ?></Data></Cell>
                        <Cell ss:StyleID="sDataLeft<?php echo $alt; ?>"><Data ss:Type="String"><?php echo htmlspecialchars($r->color); ?></Data></Cell>
                        <Cell ss:StyleID="<?php echo $stockStyle; ?>"><Data ss:Type="String"><?php echo $r->stock; ?> uds</Data></Cell>
                    </Row>
                <?php endforeach; ?>

                <?php if ($inventario->isEmpty()): ?>
                    <Row ss:Height="24">
                        <Cell ss:MergeAcross="5" ss:StyleID="sDataCenter"><Data ss:Type="String">Catálogo de productos vacío.</Data></Cell>
                    </Row>
                <?php endif; ?>
            </Table>
        </Worksheet>
    <?php endif; ?>

    <?php if ($incluirDevoluciones): ?>
        <Worksheet ss:Name="Devoluciones">
            <Table ss:ExpandedColumnCount="6" x:FullColumns="1" x:FullRows="1" ss:DefaultRowHeight="22">
                <Column ss:Width="110" />
                <Column ss:Width="110" />
                <Column ss:Width="160" />
                <Column ss:Width="260" />
                <Column ss:Width="140" />
                <Column ss:Width="150" />

                <Row ss:Height="40">
                    <Cell ss:MergeAcross="3" ss:StyleID="sTitle"><Data ss:Type="String">Its Fashion</Data></Cell>
                    <Cell ss:MergeAcross="1" ss:StyleID="sMetaRight"><Data ss:Type="String">Generado por: <?php echo htmlspecialchars((Auth::user()?->nombre ?? 'Admin') . ' ' . (Auth::user()?->apellido ?? 'General')); ?></Data></Cell>
                </Row>
                <Row ss:Height="25">
                    <Cell ss:MergeAcross="3" ss:StyleID="sSubtitle"><Data ss:Type="String">Historial de Devoluciones y Reembolsos</Data></Cell>
                    <Cell ss:MergeAcross="1" ss:StyleID="sMetaRight"><Data ss:Type="String">Fecha: <?php echo now()->format('d/m/Y H:i'); ?></Data></Cell>
                </Row>
                <Row ss:Height="20">
                    <Cell ss:MergeAcross="5" ss:StyleID="sMeta"><Data ss:Type="String">Rango de evaluación: <?php echo date('d/m/Y', strtotime($desde)); ?> al <?php echo date('d/m/Y', strtotime($hasta)); ?></Data></Cell>
                </Row>
                <Row ss:Height="15" />
                <Row ss:Height="30">
                    <Cell ss:MergeAcross="5" ss:StyleID="sSectionHeader"><Data ss:Type="String">Devoluciones Solicitadas</Data></Cell>
                </Row>
                <Row ss:Height="10" />

                <!-- Headers -->
                <Row ss:Height="26">
                    <Cell ss:StyleID="sTableHeader"><Data ss:Type="String">ID Dev.</Data></Cell>
                    <Cell ss:StyleID="sTableHeader"><Data ss:Type="String">ID Venta</Data></Cell>
                    <Cell ss:StyleID="sTableHeader"><Data ss:Type="String">Fecha</Data></Cell>
                    <Cell ss:MergeAcross="1" ss:StyleID="sTableHeader"><Data ss:Type="String">Cliente / Motivo</Data></Cell>
                    <Cell ss:StyleID="sTableHeaderRight"><Data ss:Type="String">Monto Devuelto</Data></Cell>
                </Row>

                <!-- Data -->
                <?php foreach ($devoluciones as $idx => $d): ?>
                    <?php $alt = ($idx % 2 === 0) ? '' : 'Alt'; ?>
                    <Row ss:Height="32">
                        <Cell ss:StyleID="sStatusRed<?php echo $alt; ?>"><Data ss:Type="String">DEV-#<?php echo str_pad($d->id, 3, '0', STR_PAD_LEFT); ?></Data></Cell>
                        <Cell ss:StyleID="sDataLeft<?php echo $alt; ?>"><Data ss:Type="String">#<?php echo str_pad($d->venta_id, 5, '0', STR_PAD_LEFT); ?></Data></Cell>
                        <Cell ss:StyleID="sDataLeft<?php echo $alt; ?>"><Data ss:Type="String"><?php echo $d->fecha ? $d->fecha->format('d/m/Y H:i') : '—'; ?></Data></Cell>
                        <Cell ss:MergeAcross="1" ss:StyleID="sDataLeft<?php echo $alt; ?>"><Data ss:Type="String"><?php echo htmlspecialchars($d->cliente_nombre . ' ' . $d->cliente_apellido . ' - Motivo: "' . $d->motivo . '"'); ?></Data></Cell>
                        <Cell ss:StyleID="sStatusRed<?php echo $alt; ?>"><Data ss:Type="String">-$<?php echo number_format($d->total_devolucion, 2); ?></Data></Cell>
                    </Row>
                <?php endforeach; ?>

                <?php if ($devoluciones->isEmpty()): ?>
                    <Row ss:Height="24">
                        <Cell ss:MergeAcross="5" ss:StyleID="sDataCenter"><Data ss:Type="String">No se registraron devoluciones.</Data></Cell>
                    </Row>
                <?php endif; ?>
            </Table>
        </Worksheet>
    <?php endif; ?>
</Workbook>