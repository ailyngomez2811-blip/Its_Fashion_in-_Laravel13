<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Ejecutivo - Its Fashion</title>
    <style>
        @page {
            margin: 80px 30px 48px 30px;
        }
        
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            font-size: 10px;
            line-height: 1.45;
        }

        /* Cabecera Fija */
        header {
            position: fixed;
            top: -68px;
            left: 0px;
            right: 0px;
            height: 56px;
            border-bottom: 1.5px solid #cbd5e1;
            padding-bottom: 6px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-logo-cell {
            vertical-align: middle;
            text-align: left;
            width: 52%;
        }

        .header-logo-img {
            height: 46px;
            vertical-align: middle;
        }

        .header-meta-cell {
            vertical-align: middle;
            text-align: right;
            font-size: 9px;
            color: #475569;
            line-height: 1.45;
        }

        .header-meta-cell strong {
            color: #0f172a;
        }

        .doc-badge {
            display: inline-block;
            background-color: #e0f2fe;
            border: 1px solid #bae6fd;
            color: #0369a1;
            font-size: 8px;
            font-weight: 800;
            padding: 2px 7px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        /* Pie de página */
        footer {
            position: fixed;
            bottom: -32px;
            left: 0px;
            right: 0px;
            height: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
            font-size: 8px;
            color: #94a3b8;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .page-number:after {
            content: counter(page);
        }

        /* Contenedor Principal */
        .content {
            margin-top: 4px;
        }

        /* Banner Hero Principal */
        .hero-banner {
            background-color: #061d4a;
            border-radius: 7px;
            padding: 11px 15px;
            margin-bottom: 14px;
            color: #ffffff;
        }

        .hero-table {
            width: 100%;
            border-collapse: collapse;
        }

        .hero-title {
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #ffffff;
            margin: 0 0 3px 0;
            text-transform: uppercase;
        }

        .hero-sub {
            font-size: 9px;
            color: #93c5fd;
            margin: 0;
        }

        .hero-date-badge {
            background-color: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 5px;
            padding: 6px 11px;
            text-align: right;
            font-size: 8.5px;
            color: #e2e8f0;
            display: inline-block;
        }

        /* Tarjetas KPIs */
        .kpi-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 0;
            margin-left: -6px;
            margin-right: -6px;
            margin-bottom: 14px;
        }

        .kpi-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 7px;
            padding: 9px 7px;
            vertical-align: middle;
        }

        .kpi-card.card-blue {
            background-color: #f8fafc;
            border-color: #cbd5e1;
        }

        .kpi-card.card-green {
            background-color: #f0fdf4;
            border-color: #bbf7d0;
        }

        .kpi-card.card-amber {
            background-color: #fffbeb;
            border-color: #fde68a;
        }

        .kpi-card.card-purple {
            background-color: #faf5ff;
            border-color: #ddd6fe;
        }

        .kpi-circle-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            text-align: center;
            line-height: 32px;
            display: inline-block;
            vertical-align: middle;
        }

        .icon-blue { background-color: #e0f2fe; }
        .icon-green { background-color: #dcfce7; }
        .icon-amber { background-color: #fef3c7; }
        .icon-purple { background-color: #ede9fe; }

        .kpi-lbl {
            font-size: 7.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .kpi-lbl-blue { color: #2563eb; }
        .kpi-lbl-green { color: #059669; }
        .kpi-lbl-amber { color: #d97706; }
        .kpi-lbl-purple { color: #7c3aed; }

        .kpi-val {
            font-size: 15px;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 2px;
        }

        .kpi-sub {
            font-size: 7px;
            color: #64748b;
        }

        /* Títulos de Sección */
        .section-header {
            margin-top: 14px;
            margin-bottom: 8px;
            padding-bottom: 4px;
            page-break-after: avoid;
        }

        .section-title {
            font-size: 11px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            vertical-align: middle;
        }

        /* Tablas de Datos Transparentes */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            page-break-inside: auto;
            background: transparent;
        }

        table.data-table thead {
            display: table-header-group;
        }

        table.data-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
            background: transparent;
        }

        table.data-table th {
            background-color: #002d72;
            color: #ffffff;
            font-weight: 700;
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 7px 9px;
            border: none;
            text-align: left;
        }

        table.data-table td {
            padding: 7px 9px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9.5px;
            color: #334155;
            vertical-align: middle;
            background: transparent;
        }

        table.data-table tbody tr td {
            background-color: transparent !important;
        }

        /* Total Consolidado Card */
        .total-card {
            background-color: #f0f7ff;
            border: 1px solid #bae6fd;
            border-radius: 7px;
            padding: 10px 14px;
            margin-top: 8px;
            margin-bottom: 14px;
        }

        .total-card-table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 2.5px 8px;
            border-radius: 8px;
            font-size: 7.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-success { background-color: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .badge-warning { background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-danger { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .badge-info { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: monospace; }
        .fw-bold { font-weight: 700; }
        .text-slate-900 { color: #0f172a; }
        .text-blue-600 { color: #2563eb; }
        .text-emerald-600 { color: #059669; }
        .text-red-600 { color: #dc2626; }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    @php
        // SVG Vector Icons Helper
        $svgCheck = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>');
        $svgCheckBlue = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#0369a1" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>');
        $svgDocWhite = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>');
        $svgCalendar = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#93c5fd" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>');
        $svgDollarBlue = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>');
        $svgCartGreen = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>');
        $svgReceiptAmber = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"></path><path d="M8 7h8"></path><path d="M8 12h8"></path><path d="M8 17h4"></path></svg>');
        $svgWalletPurple = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"></path><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"></path><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"></path></svg>');
        $svgDocBlue = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>');
        $svgMoneyGreen = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"></rect><circle cx="12" cy="12" r="2"></circle><path d="M6 12h.01M18 12h.01"></path></svg>');
        $svgBankBlue = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"></path><path d="M3 10h18"></path><path d="M5 6l7-3 7 3"></path><path d="M4 10v11"></path><path d="M20 10v11"></path></svg>');
        $svgPiggyBank = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1e40af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 5c-1.5 0-2.8 1.4-3 2-3.5-1.5-11-.3-11 5 0 1.8 0 3 2 4.5V20h4v-2h3v2h4v-4c1-.5 1.7-1 2-2h1l1-3h-2c0-1.5-.5-3-1-3.5"></path><circle cx="9" cy="10" r="1"></circle></svg>');
        $svgSectionList = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>');
        $svgDress = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"></path></svg>');
        $svgBox = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>');
        $svgReturn = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>');
    @endphp

    <!-- CABECERA -->
    <header>
        <table class="header-table">
            <tr>
                <td class="header-logo-cell">
                    <table style="border-collapse: collapse; border: none; margin: 0; padding: 0;">
                        <tr>
                            <td style="vertical-align: middle; padding: 0 12px 0 0; border-right: 1.5px solid #cbd5e1;">
                                @if (!empty($logoNombreBase64))
                                    <img src="{{ $logoNombreBase64 }}" style="height: 46px; vertical-align: middle;" alt="Logo">
                                @elseif (!empty($logoBase64))
                                    <img src="{{ $logoBase64 }}" style="height: 46px; vertical-align: middle;" alt="Logo">
                                @endif
                            </td>
                            <td style="vertical-align: middle; padding-left: 12px;">
                                <div style="font-family: Georgia, 'Times New Roman', serif; font-size: 22px; font-weight: bold; color: #0a2540; line-height: 1;">
                                    Its <span style="color: #2563eb;">Fashion</span>
                                </div>
                                <div style="font-size: 8px; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 1.4px; margin-top: 3px;">
                                    TIENDA DE ROPA &amp; MODA
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="header-meta-cell">
                    <span class="doc-badge">
                        <img src="{{ $svgCheckBlue }}" width="8" height="8" style="vertical-align: middle; margin-right: 3px;" alt="Check">DOCUMENTO OFICIAL
                    </span><br>
                    <strong>Generado por:</strong> {{ Auth::user()?->nombre ?? 'Admin' }} {{ Auth::user()?->apellido ?? 'General' }}<br>
                    <strong>Fecha de emisión:</strong> {{ now()->format('d/m/Y H:i') }}<br>
                    <strong>ID Informe:</strong> <span class="font-mono">#RPT-{{ date('YmdHi') }}</span>
                </td>
            </tr>
        </table>
    </header>

    <!-- PIE DE PÁGINA -->
    <footer>
        <table class="footer-table">
            <tr>
                <td style="text-align: left;">Its Fashion Store · Documento de Control Interno y Gestión Comercial</td>
                <td style="text-align: right;">Página <span class="page-number"></span></td>
            </tr>
        </table>
    </footer>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="content">
        
        <!-- Hero Header -->
        <div class="hero-banner">
            <table class="hero-table">
                <tr>
                    <td style="vertical-align: middle;">
                        <table style="border-collapse: collapse; border: none; margin: 0; padding: 0;">
                            <tr>
                                <td style="vertical-align: middle; padding-right: 10px;">
                                    <div style="background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.22); border-radius: 6px; padding: 6px 7px; line-height: 1;">
                                        <img src="{{ $svgDocWhite }}" width="14" height="14" style="vertical-align: middle;" alt="Doc">
                                    </div>
                                </td>
                                <td style="vertical-align: middle;">
                                    <h1 class="hero-title">INFORME EJECUTIVO DE OPERACIONES</h1>
                                    <p class="hero-sub">Consolidado de Actividad Comercial, Rotación de Stock e Inventario</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="text-align: right; vertical-align: middle; width: 36%;">
                        <div class="hero-date-badge">
                            <span style="color: #93c5fd; font-size: 7px; text-transform: uppercase; font-weight: bold; display: block; margin-bottom: 2px;">
                                <img src="{{ $svgCalendar }}" width="9" height="9" style="vertical-align: middle; margin-right: 2px;" alt="Cal">Periodo Evaluado
                            </span>
                            <strong>{{ date('d/m/Y', strtotime($desde)) }}</strong> al <strong>{{ date('d/m/Y', strtotime($hasta)) }}</strong>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- SECCIÓN 1: VENTAS E INGRESOS -->
        @if ($incluirVentas)
            <table class="kpi-table">
                <tr>
                    <!-- KPI 1 -->
                    <td style="width: 25%;">
                        <div class="kpi-card card-blue">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="width: 34px; vertical-align: middle; text-align: center;">
                                        <div class="kpi-circle-icon icon-blue">
                                            <img src="{{ $svgDollarBlue }}" width="16" height="16" style="vertical-align: middle;" alt="$">
                                        </div>
                                    </td>
                                    <td style="padding-left: 6px; vertical-align: middle;">
                                        <div class="kpi-lbl kpi-lbl-blue">TOTAL INGRESOS</div>
                                        <div class="kpi-val text-blue-600">${{ number_format($kpi->total_periodo ?? 0, 2) }}</div>
                                        <div class="kpi-sub">Ingresos totales del periodo</div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <!-- KPI 2 -->
                    <td style="width: 25%;">
                        <div class="kpi-card card-green">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="width: 34px; vertical-align: middle; text-align: center;">
                                        <div class="kpi-circle-icon icon-green">
                                            <img src="{{ $svgCartGreen }}" width="16" height="16" style="vertical-align: middle;" alt="Cart">
                                        </div>
                                    </td>
                                    <td style="padding-left: 6px; vertical-align: middle;">
                                        <div class="kpi-lbl kpi-lbl-green">VENTAS REALIZADAS</div>
                                        <div class="kpi-val text-emerald-600">{{ number_format($kpi->transacciones ?? 0) }}</div>
                                        <div class="kpi-sub">Número de ventas efectuadas</div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <!-- KPI 3 -->
                    <td style="width: 25%;">
                        <div class="kpi-card card-amber">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="width: 34px; vertical-align: middle; text-align: center;">
                                        <div class="kpi-circle-icon icon-amber">
                                            <img src="{{ $svgReceiptAmber }}" width="16" height="16" style="vertical-align: middle;" alt="Ticket">
                                        </div>
                                    </td>
                                    <td style="padding-left: 6px; vertical-align: middle;">
                                        <div class="kpi-lbl kpi-lbl-amber">TICKET PROMEDIO</div>
                                        <div class="kpi-val" style="color: #d97706;">${{ number_format($kpi->ticket_promedio ?? 0, 2) }}</div>
                                        <div class="kpi-sub">Promedio por venta</div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <!-- KPI 4 -->
                    <td style="width: 25%;">
                        <div class="kpi-card card-purple">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="width: 34px; vertical-align: middle; text-align: center;">
                                        <div class="kpi-circle-icon icon-purple">
                                            <img src="{{ $svgWalletPurple }}" width="16" height="16" style="vertical-align: middle;" alt="Wallet">
                                        </div>
                                    </td>
                                    <td style="padding-left: 6px; vertical-align: middle;">
                                        <div class="kpi-lbl kpi-lbl-purple">EN EFECTIVO</div>
                                        <div class="kpi-val" style="color: #7c3aed;">${{ number_format($kpi->efectivo ?? 0, 2) }}</div>
                                        <div class="kpi-sub">Total recibido en efectivo</div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>

            <div class="section-header">
                <img src="{{ $svgSectionList }}" width="13" height="13" style="vertical-align: middle; margin-right: 4px;" alt="List">
                <span class="section-title">DETALLE DE VENTAS DEL PERIODO</span>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 13%; text-align: center;">ID VENTA</th>
                        <th style="width: 18%;">FECHA Y HORA</th>
                        <th>CLIENTE</th>
                        <th style="width: 18%;">MÉTODO DE PAGO</th>
                        <th style="width: 14%; text-align: center;">ESTADO</th>
                        <th style="width: 14%; text-align: right;">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sumVentas = 0; @endphp
                    @forelse ($ventas as $v)
                        @php $sumVentas += $v->total; @endphp
                        <tr>
                            <td class="font-mono text-center fw-bold" style="color: #2563eb;">
                                <img src="{{ $svgDocBlue }}" width="10" height="10" style="vertical-align: middle; margin-right: 3px;" alt="#">#{{ str_pad($v->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td>{{ $v->fecha ? $v->fecha->format('d/m/Y H:i') : '—' }}</td>
                            <td class="fw-bold text-slate-900">{{ trim(($v->cliente?->nombre ?? 'Venta Mostrador') . ' ' . ($v->cliente?->apellido ?? '')) }}</td>
                            <td>
                                @if (stripos($v->metodo_pago, 'efectivo') !== false)
                                    <img src="{{ $svgMoneyGreen }}" width="11" height="11" style="vertical-align: middle; margin-right: 3px;" alt="Cash">
                                @else
                                    <img src="{{ $svgBankBlue }}" width="11" height="11" style="vertical-align: middle; margin-right: 3px;" alt="Bank">
                                @endif
                                {{ $v->metodo_pago }}
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $v->estado === 'Completada' ? 'badge-success' : 'badge-danger' }}">
                                    @if ($v->estado === 'Completada')
                                        <img src="{{ $svgCheck }}" width="8" height="8" style="vertical-align: middle; margin-right: 2px;" alt="✔">
                                    @endif
                                    {{ strtoupper($v->estado) }}
                                </span>
                            </td>
                            <td class="text-right fw-bold text-slate-900">${{ number_format($v->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center" style="color: #94a3b8; padding: 14px;">No se registraron ventas en este período.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if (!$ventas->isEmpty())
                <div class="total-card">
                    <table class="total-card-table">
                        <tr>
                            <td style="vertical-align: middle;">
                                <table style="border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                    <tr>
                                        <td style="vertical-align: middle; padding-right: 8px;">
                                            <img src="{{ $svgPiggyBank }}" width="20" height="20" style="vertical-align: middle;" alt="Bank">
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <div style="font-size: 11px; font-weight: 800; color: #1e3a8a;">TOTAL CONSOLIDADO DEL PERIODO</div>
                                            <div style="font-size: 8px; color: #64748b;">Suma total de ingresos generados</div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="text-align: right; vertical-align: middle;">
                                <div style="font-size: 17px; font-weight: 800; color: #1e40af;">${{ number_format($sumVentas, 2) }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
            @endif
        @endif

        <!-- SECCIÓN 2: PRODUCTOS MÁS VENDIDOS -->
        @if ($incluirProductos)
            @if ($incluirVentas) <div class="page-break"></div> @endif
            <div class="section-header">
                <img src="{{ $svgDress }}" width="13" height="13" style="vertical-align: middle; margin-right: 4px;" alt="Dress">
                <span class="section-title">RANKING DE PRENDAS MÁS VENDIDAS</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 8%; text-align: center;">POS</th>
                        <th>PRODUCTO / PRENDA</th>
                        <th style="width: 14%; text-align: center;">TALLA</th>
                        <th style="width: 14%; text-align: center;">COLOR</th>
                        <th style="width: 18%; text-align: center;">UDS. VENDIDAS</th>
                        <th style="width: 18%; text-align: right;">INGRESOS GEN.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($productos as $i => $p)
                        <tr>
                            <td class="text-center fw-bold" style="color: #64748b;">{{ $i + 1 }}</td>
                            <td class="fw-bold text-slate-900">{{ $p->nombre }}</td>
                            <td class="text-center">{{ $p->talla }}</td>
                            <td class="text-center">{{ $p->color }}</td>
                            <td class="text-center fw-bold text-blue-600">{{ $p->vendidos }} uds</td>
                            <td class="text-right fw-bold text-emerald-600">${{ number_format($p->ingresos, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center" style="color: #94a3b8; padding: 14px;">Sin transacciones de prendas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif

        <!-- SECCIÓN 3: INVENTARIO GENERAL -->
        @if ($incluirInventario)
            @if ($incluirVentas || $incluirProductos) <div class="page-break"></div> @endif
            <div class="section-header">
                <img src="{{ $svgBox }}" width="13" height="13" style="vertical-align: middle; margin-right: 4px;" alt="Box">
                <span class="section-title">ESTADO GENERAL DE INVENTARIO</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>PRODUCTO</th>
                        <th style="width: 20%;">CATEGORÍA</th>
                        <th style="width: 12%; text-align: center;">TALLA</th>
                        <th style="width: 12%; text-align: center;">COLOR</th>
                        <th style="width: 14%; text-align: center;">STOCK</th>
                        <th style="width: 14%; text-align: center;">MÍNIMO</th>
                        <th style="width: 16%; text-align: center;">ESTADO</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inventario as $r)
                        @php
                            $status = 'Disponible';
                            if ($r->stock == 0) {
                                $status = 'Agotado';
                            } elseif ($r->stock <= ($r->stock_minimo ?? 0)) {
                                $status = 'Crítico';
                            }
                            $bc = $status === 'Disponible' ? 'badge-success' : ($status === 'Crítico' ? 'badge-warning' : 'badge-danger');
                        @endphp
                        <tr>
                            <td class="fw-bold text-slate-900">{{ $r->nombre }}</td>
                            <td style="color: #475569;">{{ $r->categoria?->nombre ?? '—' }}</td>
                            <td class="text-center">{{ $r->talla }}</td>
                            <td class="text-center">{{ $r->color }}</td>
                            <td class="text-center fw-bold {{ $status !== 'Disponible' ? 'text-red-600' : '' }}">{{ $r->stock }}</td>
                            <td class="text-center" style="color: #64748b;">{{ $r->stock_minimo ?? 0 }}</td>
                            <td class="text-center"><span class="badge {{ $bc }}">{{ strtoupper($status) }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center" style="color: #94a3b8; padding: 14px;">Catálogo de productos vacío.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif

        <!-- SECCIÓN 4: DEVOLUCIONES -->
        @if ($incluirDevoluciones)
            @if ($incluirVentas || $incluirProductos || $incluirInventario) <div class="page-break"></div> @endif
            <div class="section-header">
                <img src="{{ $svgReturn }}" width="13" height="13" style="vertical-align: middle; margin-right: 4px;" alt="Return">
                <span class="section-title">HISTORIAL DE DEVOLUCIONES Y REEMBOLSOS</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 14%; text-align: center;">ID DEV.</th>
                        <th style="width: 14%; text-align: center;">ID VENTA</th>
                        <th style="width: 16%;">FECHA</th>
                        <th>CLIENTE</th>
                        <th>MOTIVO</th>
                        <th style="width: 16%; text-align: right;">TOTAL DEVUELTO</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($devoluciones as $d)
                        <tr>
                            <td class="font-mono text-center fw-bold text-red-600">DEV-#{{ str_pad($d->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td class="font-mono text-center">#{{ str_pad($d->venta_id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $d->fecha ? date('d/m/Y H:i', strtotime($d->fecha)) : '—' }}</td>
                            <td class="fw-bold text-slate-900">{{ trim(($d->cliente_nombre ?? '') . ' ' . ($d->cliente_apellido ?? '')) }}</td>
                            <td style="color: #475569;"><em>"{{ $d->motivo }}"</em></td>
                            <td class="text-right fw-bold text-red-600">-${{ number_format($d->total_devolucion, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center" style="color: #94a3b8; padding: 14px;">No se registraron devoluciones en este período.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif

    </div>

</body>
</html>



