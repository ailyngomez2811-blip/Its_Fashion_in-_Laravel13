@extends('layouts.sidebaradmin')

@section('titulo', 'Proveedores')

@push('estilos')
<style>
    /* Efecto hover en las filas de la tabla */
    .trow:hover {
        background: #f8fafc;
    }

    /* Estilo para etiquetas/insignias */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    /* Contenedor del Modal */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, .5);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 16px;
    }

    /* Caja del Modal */
    .modal-box {
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 25px 60px rgba(0, 0, 0, .15);
        width: 100%;
        max-width: 560px;
        max-height: 92vh;
        display: flex;
        flex-direction: column;
    }

    /* Animación del modal */
    @keyframes scaleUp {
        from {
            opacity: 0;
            transform: scale(.97)
        }
        to {
            opacity: 1;
            transform: scale(1)
        }
    }

    .scale-up {
        animation: scaleUp .3s ease-out;
    }

    /* Gradiente acentuado */
    .gradient-accent {
        background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
    }
</style>
@endpush

@section('content')
<div class="p-6 font-sans">

    <!-- Encabezado de la página -->
    <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-brand-accent shadow-sm border border-blue-100">
                <i class="fas fa-truck-loading text-lg"></i>
            </div>
            <h1 class="text-xl md:text-2xl font-serif font-bold text-brand-dark">Proveedores</h1>
        </div>
        @if (auth()->user()->rol_id === 1)
        <button onclick="openModal('create')" 
            class="flex items-center gap-2 px-4 py-2.5 bg-brand-accent text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all" 
            style="box-shadow: 0 4px 12px rgba(59,130,246,.25);">
            <i class="fas fa-plus text-xs"></i> <span>Nuevo proveedor</span>
        </button>
        @endif
    </div>

    <!-- Directorio de proveedores -->
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 flex-wrap gap-3">
            <div>
                <h3 class="font-bold text-slate-800">Directorio de proveedores</h3>
                <p class="text-xs text-slate-500 mt-0.5" id="contador-provs">{{ count($proveedores) }} proveedor(es) registrado(s)</p>
            </div>
            <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                    <i class="fas fa-search text-sm"></i>
                </span>
                <input type="text" id="search-input" placeholder="Nombre o documento..." oninput="filterTable()"
                    class="pl-10 pr-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 focus:outline-none transition w-64">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr>
                        @foreach (['Proveedor', 'Contacto', 'Teléfono', 'Email', 'Documento', ''] as $h)
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="table-body">
                    @forelse ($proveedores as $p)
                        <tr class="trow" id="prov-{{ $p->id }}" data-search="{{ strtolower($p->nombre . ' ' . $p->documento) }}">
                            <td class="px-6 py-4 border-b border-slate-50">
                                <p class="font-semibold text-slate-800 text-sm">{{ $p->nombre }}</p>
                                <p class="text-xs text-slate-400">{{ $p->direccion ?? '' }}</p>
                            </td>
                            <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-700">{{ $p->contacto }}</td>
                            <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-700">{{ $p->telefono }}</td>
                            <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-505 text-slate-500">{{ $p->email ?? '—' }}</td>
                            <td class="px-6 py-4 border-b border-slate-50">
                                <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-mono">{{ $p->documento }}</span>
                            </td>
                            <td class="px-6 py-4 border-b border-slate-50">
                                @if (auth()->user()->rol_id === 1)
                                    <div class="flex items-center gap-2">
                                        <!-- Botón Editar -->
                                        <button onclick="openModal('edit', @json($p))"
                                            class="w-8 h-8 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center transition"
                                            title="Editar proveedor">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        <!-- Botón Eliminar -->
                                        <button onclick="eliminarProv({{ $p->id }}, '{{ addslashes($p->nombre) }}')"
                                            class="w-8 h-8 bg-red-50 hover:bg-red-100 text-red-500 rounded-lg flex items-center justify-center transition"
                                            title="Eliminar proveedor">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                                <i class="fas fa-truck text-4xl mb-3 block opacity-20"></i>
                                <p class="text-sm">No hay proveedores registrados aún</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="empty-state" class="hidden py-16 text-center text-slate-400">
            <i class="fas fa-search text-4xl mb-3 block opacity-30"></i>
            <p class="text-sm font-medium">No se encontraron proveedores</p>
        </div>
    </div>
</div>

<!-- MODAL CREAR / EDITAR -->
@if (auth()->user()->rol_id === 1)
<div id="modal-prov" class="modal-overlay hidden">
    <div class="modal-box scale-up">
        <div class="flex items-center justify-between p-7 pb-5 border-b border-slate-100 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-brand-accent rounded-xl flex items-center justify-center">
                    <i class="fas fa-truck-loading text-white text-sm"></i>
                </div>
                <h3 id="modal-title" class="text-lg font-bold text-slate-800">Nuevo proveedor</h3>
            </div>
            <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <form id="form-prov" method="POST" action="" class="p-7 overflow-y-auto space-y-4">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="">

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Nombre *</label>
                    <input type="text" name="nombre" id="prov-nombre" required placeholder="Nombre del proveedor"
                        class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Contacto *</label>
                    <input type="text" name="contacto" id="prov-contacto" required placeholder="Nombre del contacto"
                        class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Documento</label>
                    <input type="text" name="documento" id="prov-doc" placeholder="NIT o cédula"
                        class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Teléfono *</label>
                    <input type="text" name="telefono" id="prov-tel" required placeholder="Teléfono"
                        class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Email</label>
                    <input type="email" name="email" id="prov-email" placeholder="correo@proveedor.com"
                        class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                </div>
                <div class="col-span-2">
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Dirección</label>
                    <input type="text" name="direccion" id="prov-dir" placeholder="Dirección"
                        class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal()" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition text-sm">Cancelar</button>
                <button type="submit" class="flex-1 py-3 bg-brand-accent text-white font-semibold rounded-xl transition text-sm hover:shadow-lg" style="box-shadow:0 4px 12px rgba(59,130,246,.25);">
                    <i class="fas fa-save mr-2 text-xs"></i><span id="btn-text">Guardar</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- FORMULARIO OCULTO PARA ELIMINACIÓN -->
<form id="delete-form" method="POST" action="" class="hidden">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<!-- SweetAlert2 para notificaciones -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const STORE_URL = "{{ route('proveedores.store') }}";
    const BASE_URL = "{{ url('/proveedores') }}";

    // ── Filtrado local reactivo ────────────────────────────────────────────────────
    function filterTable() {
        const q = document.getElementById('search-input').value.toLowerCase();
        let visible = 0;
        
        document.querySelectorAll('#table-body tr').forEach(row => {
            if (row.dataset.search) {
                const show = !q || row.dataset.search.includes(q);
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            }
        });

        document.getElementById('empty-state').classList.toggle('hidden', visible > 0);
    }

    // ── Modal Apertura y Cierre ────────────────────────────────────────────────────
    @if (auth()->user()->rol_id === 1)
    function openModal(mode, data = null) {
        const modal = document.getElementById('modal-prov');
        const form = document.getElementById('form-prov');
        const methodInput = document.getElementById('form-method');
        const title = document.getElementById('modal-title');
        const btnText = document.getElementById('btn-text');

        form.reset();

        if (mode === 'create') {
            form.action = STORE_URL;
            methodInput.value = 'POST';
            title.textContent = 'Nuevo proveedor';
            btnText.textContent = 'Guardar';
            modal.classList.remove('hidden');
        } else if (mode === 'edit' && data) {
            form.action = `${BASE_URL}/${data.id}`;
            methodInput.value = 'PUT';
            document.getElementById('prov-nombre').value = data.nombre;
            document.getElementById('prov-contacto').value = data.contacto;
            document.getElementById('prov-doc').value = data.documento;
            document.getElementById('prov-tel').value = data.telefono;
            document.getElementById('prov-email').value = data.email || '';
            document.getElementById('prov-dir').value = data.direccion || '';
            
            title.textContent = 'Editar proveedor';
            btnText.textContent = 'Actualizar';
            modal.classList.remove('hidden');
        }
    }

    function closeModal() {
        document.getElementById('modal-prov').classList.add('hidden');
    }

    // Cierre del modal con tecla Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal();
    });

    // Control de foco visual en los campos del formulario
    document.querySelectorAll('#form-prov input').forEach(el => {
        el.addEventListener('focus', () => {
            el.style.borderColor = '#3b82f6';
            el.style.boxShadow = '0 0 0 3px rgba(59,130,246,.15)';
        });
        el.addEventListener('blur', () => {
            el.style.borderColor = '';
            el.style.boxShadow = '';
        });
    });
    // Confirmar eliminación con SweetAlert2
    function eliminarProv(id, nombre) {
        Swal.fire({
            title: '¿Eliminar proveedor?',
            text: `¿Estás seguro de eliminar el proveedor "${nombre}"? Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('delete-form');
                form.action = `${BASE_URL}/${id}`;
                form.submit();
            }
        });
    }
    @endif

    // Alertas rápidas Toast mediante SweetAlert2
    function showToast(msg, type = 'success') {
        Swal.fire({
            icon: type,
            title: msg,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    // Notificaciones flash de redirección (Laravel session)
    @if (session('toast'))
        document.addEventListener('DOMContentLoaded', () => {
            showToast(@json(session('toast')['text']), @json(session('toast')['type']));
        });
    @endif

    // Notificaciones de errores de validación de backend
    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'error',
                title: 'Error de Validación',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'Entendido'
            });
        });
    @endif
</script>
@endpush
