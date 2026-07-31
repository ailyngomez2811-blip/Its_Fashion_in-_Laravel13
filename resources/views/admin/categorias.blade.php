@extends('layouts.sidebaradmin')

@section('titulo', 'Categorías')

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
        max-width: 480px;
        max-height: 92vh;
        display: flex;
        flex-direction: column;
    }

    /* Animación de apertura del modal */
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

    /* Gradiente para botones destacados */
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
                <i class="fas fa-tags text-lg"></i>
            </div>
            <h1 class="text-xl md:text-2xl font-serif font-bold text-brand-dark">Categorías de Productos</h1>
        </div>
        <button onclick="openModal('create')" 
            class="flex items-center gap-2 px-4 py-2.5 bg-brand-accent text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all" 
            style="box-shadow: 0 4px 12px rgba(59,130,246,.25);">
            <i class="fas fa-plus text-xs"></i> <span>Nueva categoría</span>
        </button>
    </div>


    <!-- Tabla de Categorías -->
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 flex-wrap gap-3">
            <div>
                <h3 class="font-bold text-slate-800">Categorías registradas</h3>
                <p class="text-xs text-slate-500 mt-0.5" id="contador-cats">{{ count($categorias) }} categoría(s) en el sistema</p>
            </div>
            <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                    <i class="fas fa-search text-sm"></i>
                </span>
                <input type="text" id="search-input" placeholder="Buscar categoría..." oninput="filterTable()"
                    class="pl-10 pr-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 transition w-60" style="outline:none;">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100">Nombre</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100">Descripción</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100">Acciones</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @forelse ($categorias as $c)
                        <tr class="trow" id="cat-{{ $c->id }}" data-search="{{ strtolower($c->nombre . ' ' . $c->descripcion) }}">
                            <td class="px-6 py-4 border-b border-slate-50">
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-xl text-sm font-semibold">
                                    <i class="fas fa-tag text-xs"></i>{{ $c->nombre }}
                                </span>
                            </td>
                            <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-500">
                                {{ $c->descripcion ?? '—' }}
                            </td>
                            <td class="px-6 py-4 border-b border-slate-50">
                                <div class="flex items-center gap-2">
                                    <!-- Botón Editar -->
                                    <button onclick="openModal('edit', @json($c))"
                                        class="w-8 h-8 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center transition"
                                        title="Editar categoría">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                    <!-- Botón Eliminar -->
                                    <button onclick="eliminarCat({{ $c->id }}, '{{ addslashes($c->nombre) }}')"
                                        class="w-8 h-8 bg-red-50 hover:bg-red-100 text-red-500 rounded-lg flex items-center justify-center transition"
                                        title="Eliminar categoría">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-16 text-center text-slate-400">
                                <i class="fas fa-tags text-4xl mb-3 block opacity-20"></i>
                                <p class="text-sm">No hay categorías registradas aún</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- Paginador de Categorías -->
            <div id="categories-pagination" class="px-6 py-4 border-t border-slate-100 flex items-center justify-between flex-wrap gap-2 bg-slate-50/50">
                <span class="text-xs text-slate-500 font-medium" id="categories-page-info">Mostrando registros 1-10</span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="prevCategoriesPage()" id="btn-categories-prev" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-600 text-xs font-semibold hover:bg-slate-50 disabled:opacity-50 transition">Anterior</button>
                    <button type="button" onclick="nextCategoriesPage()" id="btn-categories-next" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-600 text-xs font-semibold hover:bg-slate-50 disabled:opacity-50 transition">Siguiente</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DE FORMULARIO -->
<div id="modal-cat" class="modal-overlay hidden">
    <div class="modal-box scale-up p-7">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 gradient-accent rounded-xl flex items-center justify-center">
                    <i class="fas fa-tags text-white text-sm"></i>
                </div>
                <h3 id="modal-title" class="text-lg font-bold text-slate-800">Nueva categoría</h3>
            </div>
            <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <form id="form-cat" method="POST" action="" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="">

            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Nombre *</label>
                <input type="text" name="nombre" id="cat-nombre" required placeholder="Ej: Blusas"
                    class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                <p class="err hidden text-xs text-red-500 mt-1 ml-1" id="err-nombre">
                    <i class="fas fa-exclamation-circle mr-1"></i>El nombre es obligatorio.
                </p>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Descripción</label>
                <textarea name="descripcion" id="cat-desc" rows="3" placeholder="Descripción opcional..."
                    class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 resize-none focus:outline-none transition"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal()" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition text-sm">Cancelar</button>
                <button type="submit" class="flex-1 py-3 gradient-accent text-white font-semibold rounded-xl transition text-sm hover:shadow-lg" style="box-shadow:0 4px 12px rgba(59,130,246,.25);">
                    <i class="fas fa-save mr-2 text-xs"></i><span id="btn-text">Guardar</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- FORMULARIO OCULTO PARA ELIMINACIÓN -->
<form id="delete-form" method="POST" action="" class="hidden">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<!-- SweetAlert2 para confirmaciones elegantes -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const STORE_URL = "{{ route('categorias.store') }}";
    const BASE_URL = "{{ url('/categorias') }}";

    let currentCategoriesPage = 1;
    const categoriesRecordsPerPage = 10;

    document.addEventListener('DOMContentLoaded', () => {
        applyCategoriesPagination();
    });

    function applyCategoriesPagination() {
        const rows = Array.from(document.querySelectorAll('#table-body tr')).filter(row => row.dataset.search && row.style.display !== 'none');
        const infoSpan = document.getElementById('categories-page-info');
        const prevBtn = document.getElementById('btn-categories-prev');
        const nextBtn = document.getElementById('btn-categories-next');

        const total = rows.length;
        const totalPages = Math.ceil(total / categoriesRecordsPerPage) || 1;

        if (currentCategoriesPage > totalPages) {
            currentCategoriesPage = totalPages;
        }

        const startIdx = (currentCategoriesPage - 1) * categoriesRecordsPerPage;
        const endIdx = startIdx + categoriesRecordsPerPage;

        const allRows = document.querySelectorAll('#table-body tr');
        let matchedIdx = 0;
        allRows.forEach(row => {
            if (row.dataset.search) {
                if (row.getAttribute('data-filtered') === 'true') {
                    row.style.display = 'none';
                } else {
                    if (matchedIdx >= startIdx && matchedIdx < endIdx) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                    matchedIdx++;
                }
            }
        });

        if (total === 0) {
            infoSpan.textContent = "Mostrando registros 0 de 0";
            prevBtn.disabled = true;
            nextBtn.disabled = true;
        } else {
            infoSpan.textContent = `Mostrando registros ${startIdx + 1}-${Math.min(endIdx, total)} de ${total}`;
            prevBtn.disabled = currentCategoriesPage === 1;
            nextBtn.disabled = currentCategoriesPage === totalPages;
        }
    }

    function prevCategoriesPage() {
        if (currentCategoriesPage > 1) {
            currentCategoriesPage--;
            applyCategoriesPagination();
        }
    }

    function nextCategoriesPage() {
        currentCategoriesPage++;
        applyCategoriesPagination();
    }

    // Filtrar la tabla de manera reactiva localmente
    function filterTable() {
        const q = document.getElementById('search-input').value.toLowerCase();
        let visible = 0;

        document.querySelectorAll('#table-body tr').forEach(row => {
            if (row.dataset.search) {
                const show = row.dataset.search.includes(q);
                if (show) {
                    row.removeAttribute('data-filtered');
                    visible++;
                } else {
                    row.setAttribute('data-filtered', 'true');
                }
            }
        });

        currentCategoriesPage = 1;
        applyCategoriesPagination();
    }

    // Abrir modal configurado para creación o edición
    function openModal(mode, data = null) {
        const modal = document.getElementById('modal-cat');
        const form = document.getElementById('form-cat');
        const methodInput = document.getElementById('form-method');
        const title = document.getElementById('modal-title');
        const btnText = document.getElementById('btn-text');

        // Limpiar errores previos
        document.getElementById('err-nombre').classList.add('hidden');
        document.getElementById('cat-nombre').style.borderColor = '';

        if (mode === 'create') {
            form.action = STORE_URL;
            methodInput.value = 'POST';
            document.getElementById('cat-nombre').value = '';
            document.getElementById('cat-desc').value = '';
            title.textContent = 'Nueva categoría';
            btnText.textContent = 'Guardar';
        } else if (mode === 'edit' && data) {
            form.action = `${BASE_URL}/${data.id}`;
            methodInput.value = 'PUT';
            document.getElementById('cat-nombre').value = data.nombre;
            document.getElementById('cat-desc').value = data.descripcion || '';
            title.textContent = 'Editar categoría';
            btnText.textContent = 'Actualizar';
        }

        modal.classList.remove('hidden');
    }

    // Cerrar modal
    function closeModal() {
        document.getElementById('modal-cat').classList.add('hidden');
    }

    // Confirmar eliminación con SweetAlert2
    function eliminarCat(id, nombre) {
        Swal.fire({
            title: '¿Eliminar categoría?',
            text: `¿Estás seguro de eliminar la categoría "${nombre}"? Esta acción no se puede deshacer.`,
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

    // Redefinido para usar SweetAlert2 en modo Toast
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

    // Escuchar el evento Escape para cerrar el modal
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal();
    });

    // Control de foco visual en inputs
    document.querySelectorAll('#form-cat input, #form-cat textarea').forEach(el => {
        el.addEventListener('focus', () => {
            el.style.borderColor = '#3b82f6';
            el.style.boxShadow = '0 0 0 3px rgba(59,130,246,.15)';
        });
        el.addEventListener('blur', () => {
            el.style.borderColor = '';
            el.style.boxShadow = '';
        });
    });

    // Validar en el cliente antes de enviar el formulario
    document.getElementById('form-cat').addEventListener('submit', function(e) {
        const nombreInput = document.getElementById('cat-nombre');
        if (!nombreInput.value.trim()) {
            e.preventDefault();
            nombreInput.style.borderColor = '#ef4444';
            document.getElementById('err-nombre').classList.remove('hidden');
        }
    });

    // Mostrar Toast si existe sesión de redirección con "toast" de Laravel
    @if (session('toast'))
        document.addEventListener('DOMContentLoaded', () => {
            showToast(@json(session('toast')['text']), @json(session('toast')['type']));
        });
    @endif
</script>
@endpush
