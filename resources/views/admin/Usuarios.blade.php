@extends('layouts.sidebaradmin')

@section('titulo', 'Gestión de Usuarios')

@push('estilos')
<style>
    .trow:hover {
        background: #f8fafc;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

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

    .modal-box {
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 25px 60px rgba(0, 0, 0, .15);
        width: 100%;
        max-width: 520px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }

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

    .toggle {
        width: 44px;
        height: 24px;
        border-radius: 12px;
        position: relative;
        cursor: pointer;
        transition: background .2s;
        display: inline-block;
        flex-shrink: 0;
    }

    .toggle::after {
        content: '';
        position: absolute;
        top: 3px;
        left: 3px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: white;
        transition: transform .2s;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .2);
    }

    .toggle.on {
        background: #2563eb;
    }

    .toggle.off {
        background: #cbd5e1;
    }

    .toggle.on::after {
        transform: translateX(20px);
    }
</style>
@endpush

@section('content')
<div class="p-6 font-sans">

    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-brand-accent shadow-sm border border-blue-100">
            <i class="fas fa-users text-lg"></i>
        </div>
        <h1 class="text-xl md:text-2xl font-serif font-bold text-brand-dark">Gestión de Usuarios</h1>
    </div>

    <!-- Toast -->
    <div id="toast" class="hidden fixed bottom-6 right-6 z-50 flex items-start gap-3 px-5 py-4 rounded-2xl shadow-2xl bg-white max-w-xs" style="border-left:4px solid #3b82f6;">
        <i id="toast-icon" class="fas fa-check-circle text-blue-500 mt-0.5 flex-shrink-0"></i>
        <span id="toast-text" class="text-slate-700 text-sm font-medium flex-1"></span>
    </div>

    @if (session('toast'))
    <script>
        document.addEventListener('DOMContentLoaded', () => showToast(@json(session('toast')['text']), @json(session('toast')['type'])));
    </script>
    @endif

    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 flex-wrap gap-3">
            <div>
                <h3 class="font-bold text-slate-800">Usuarios del sistema</h3>
                <p class="text-xs text-slate-500 mt-0.5">Gestiona cuentas, roles y permisos</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none"><i class="fas fa-search text-sm"></i></span>
                    <input type="text" id="search-input" placeholder="Buscar usuario..." oninput="filterTable()"
                        class="pl-10 pr-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 transition w-60" style="outline:none;">
                </div>
                <button onclick="openModal('create')"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-accent text-white text-sm font-semibold rounded-xl transition-all hover:shadow-lg hover:-translate-y-0.5"
                    style="box-shadow:0 4px 12px rgba(59,130,246,.25);">
                    <i class="fas fa-plus text-xs"></i> Nuevo usuario
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr>
                        @foreach (['Usuario', 'Contacto', 'Rol', 'Estado', 'Registro', ''] as $h)
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="table-body">
                    @forelse ($users as $u)
                    @php
                    $ini = strtoupper(substr($u->nombre, 0, 1) . substr($u->apellido, 0, 1));
                    $toggleClass = $u->estado === 'Activo' ? 'on' : 'off';
                    @endphp
                    <tr class="trow" data-search="{{ strtolower("{$u->nombre} {$u->apellido} {$u->username} {$u->email}") }}">
                        <td class="px-6 py-4 border-b border-slate-50">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-brand-accent rounded-xl flex items-center justify-center text-white text-xs font-bold flex-shrink-0">{{ $ini }}</div>
                                <div>
                                    <p class="font-semibold text-slate-800 text-sm">{{ $u->nombre }} {{ $u->apellido }}</p>
                                    <p class="text-xs text-slate-500">{{ '@'.$u->username }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 border-b border-slate-50">
                            <p class="text-xs text-slate-700">{{ $u->email }}</p>
                            <p class="text-xs text-slate-500">{{ $u->telefono }}</p>
                        </td>
                        <td class="px-6 py-4 border-b border-slate-50">
                            @if ($u->rol_id === 1)
                            <span class="badge bg-blue-100 text-blue-500">Administrador</span>
                            @else
                            <span class="badge bg-amber-100 text-amber-700">Empleado</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 border-b border-slate-50">
                            <div class="flex items-center gap-2 cursor-pointer" onclick="toggleEstado(this, {{ $u->id }})">
                                <div class="toggle {{ $toggleClass }}"></div>
                                <span class="text-xs text-slate-600 estado-label">{{ $u->estado }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 border-b border-slate-50 text-xs text-slate-500">{{ $u->fecha_registro?->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 border-b border-slate-50">
                            <button
                                onclick="openModal('edit', this.dataset)"
                                data-id="{{ $u->id }}"
                                data-nombre="{{ $u->nombre }}"
                                data-apellido="{{ $u->apellido }}"
                                data-username="{{ $u->username }}"
                                data-email="{{ $u->email }}"
                                data-telefono="{{ $u->telefono }}"
                                data-estado="{{ $u->estado }}"
                                data-rol-id="{{ $u->rol_id }}"
                                class="w-8 h-8 bg-blue-50 hover:bg-blue-100 rounded-lg flex items-center justify-center text-blue-500 transition">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">No hay empleados registrados aún.</td>
                    </tr>
                    @endforelse
                </tbody>
            <div id="pagination" class="px-6 py-4 border-t border-slate-100 flex items-center justify-between flex-wrap gap-2 bg-slate-50/50">
                <span class="text-xs text-slate-500 font-medium" id="page-info">Mostrando registros 1-10</span>
                <div class="flex items-center gap-2">
                    <button onclick="prevPage()" id="btn-prev" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-600 text-xs font-semibold hover:bg-slate-50 disabled:opacity-50 transition">Anterior</button>
                    <button onclick="nextPage()" id="btn-next" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-600 text-xs font-semibold hover:bg-slate-50 disabled:opacity-50 transition">Siguiente</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL -->
<div id="modal" class="modal-overlay hidden">
    <div class="modal-box scale-up">
        <div class="flex items-start justify-between p-7 pb-0">
            <div>
                <h3 id="modal-title" class="text-xl font-bold text-slate-800">Crear nuevo usuario</h3>
                <p class="text-sm text-slate-500 mt-1">Complete la información del usuario</p>
            </div>
            <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition flex-shrink-0 ml-4">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <div class="p-7 overflow-y-auto">
            <form id="user-form" method="POST" onsubmit="submitForm(event)" novalidate>
                @csrf
                <input type="hidden" name="_method" id="form-method" value="">

                <div class="grid grid-cols-2 gap-x-4">
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" id="f-nombre" name="nombre" placeholder="Nombre"
                            class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm placeholder-slate-400 transition">
                        <p class="err hidden text-xs text-red-500 mt-1 ml-1" data-for="f-nombre"><i class="fas fa-exclamation-circle mr-1"></i>Requerido</p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Apellido <span class="text-red-500">*</span></label>
                        <input type="text" id="f-apellido" name="apellido" placeholder="Apellido"
                            class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm placeholder-slate-400 transition">
                        <p class="err hidden text-xs text-red-500 mt-1 ml-1" data-for="f-apellido"><i class="fas fa-exclamation-circle mr-1"></i>Requerido</p>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nombre de usuario <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 text-sm pointer-events-none">@</span>
                        <input type="text" id="f-username" name="username" placeholder="usuario_ejemplo"
                            class="w-full pl-8 pr-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm placeholder-slate-400 transition">
                    </div>
                    <p class="err hidden text-xs text-red-500 mt-1 ml-1" data-for="f-username"><i class="fas fa-exclamation-circle mr-1"></i>Requerido</p>
                </div>

                <div class="grid grid-cols-2 gap-x-4">
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Correo electrónico <span class="text-red-500">*</span></label>
                        <input type="email" id="f-email" name="email" placeholder="correo@ejemplo.com"
                            class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm placeholder-slate-400 transition">
                        <p class="err hidden text-xs text-red-500 mt-1 ml-1" data-for="f-email"><i class="fas fa-exclamation-circle mr-1"></i>Requerido</p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Teléfono</label>
                        <input type="text" id="f-telefono" name="telefono" placeholder="3001234567"
                            class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm placeholder-slate-400 transition">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Contraseña <span class="text-red-500" id="pw-required">*</span></label>
                    <div class="relative">
                        <input type="password" id="f-password" name="password" placeholder="Mín. 8 car., mayúscula, minúscula y número" oninput="checkPwStrength()"
                            class="w-full pr-12 px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm placeholder-slate-400 transition">
                        <button type="button" id="toggle-pw" class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-slate-600 transition">
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                    <div class="mt-2 flex gap-1">
                        <div class="flex-1 h-1 rounded-full bg-slate-200" id="s1"></div>
                        <div class="flex-1 h-1 rounded-full bg-slate-200" id="s2"></div>
                        <div class="flex-1 h-1 rounded-full bg-slate-200" id="s3"></div>
                        <div class="flex-1 h-1 rounded-full bg-slate-200" id="s4"></div>
                    </div>
                    <p class="err hidden text-xs text-red-500 mt-1 ml-1" data-for="f-password"><i class="fas fa-exclamation-circle mr-1"></i>Mín. 8 car., mayúscula, minúscula y número</p>
                </div>

                <div class="grid grid-cols-2 gap-x-4">
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Rol <span class="text-red-500">*</span></label>
                        <select id="f-rol" name="rol_id" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm cursor-pointer transition">
                            @foreach ($roles as $rol)
                            <option value="{{ $rol->id }}">{{ $rol->descripcion }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Estado</label>
                        <select id="f-estado" name="estado" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm cursor-pointer transition">
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal()" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-200 transition">Cancelar</button>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-accent text-white text-sm font-semibold rounded-xl transition-all hover:shadow-lg hover:-translate-y-0.5" style="box-shadow:0 4px 12px rgba(59,130,246,.25);">
                        <i class="fas fa-save text-xs"></i> Guardar usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const STORE_URL = "{{ route('usuarios.store') }}";
    const BASE_URL = "{{ url('/usuarios') }}";

    let currentPage = 1;
    const recordsPerPage = 10;

    document.addEventListener('DOMContentLoaded', () => {
        applyPagination();
    });

    function applyPagination() {
        const rows = Array.from(document.querySelectorAll('#table-body tr.trow')).filter(row => row.style.display !== 'none');
        const infoSpan = document.getElementById('page-info');
        const prevBtn = document.getElementById('btn-prev');
        const nextBtn = document.getElementById('btn-next');

        const total = rows.length;
        const totalPages = Math.ceil(total / recordsPerPage) || 1;

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        const startIdx = (currentPage - 1) * recordsPerPage;
        const endIdx = startIdx + recordsPerPage;

        rows.forEach((row, idx) => {
            if (idx >= startIdx && idx < endIdx) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        if (total === 0) {
            infoSpan.textContent = "Mostrando registros 0 de 0";
            prevBtn.disabled = true;
            nextBtn.disabled = true;
        } else {
            infoSpan.textContent = `Mostrando registros ${startIdx + 1}-${Math.min(endIdx, total)} de ${total}`;
            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages;
        }
    }

    function prevPage() {
        if (currentPage > 1) {
            currentPage--;
            applyPagination();
        }
    }

    function nextPage() {
        currentPage++;
        applyPagination();
    }

    function filterTable() {
        const q = document.getElementById('search-input').value.toLowerCase();
        document.querySelectorAll('#table-body tr.trow').forEach(row => {
            row.style.display = row.dataset.search?.includes(q) ? '' : 'none';
        });
        currentPage = 1;
        applyPagination();
    }

    function openModal(mode, data = null) {
        document.getElementById('modal').classList.remove('hidden');
        document.getElementById('modal-title').textContent = mode === 'create' ? 'Crear nuevo usuario' : 'Editar usuario';

        const form = document.getElementById('user-form');
        clearForm();

        if (mode === 'create') {
            form.action = STORE_URL;
            document.getElementById('form-method').value = '';
        } else {
            form.action = `${BASE_URL}/${data.id}`;
            document.getElementById('form-method').value = 'PUT';
            document.getElementById('f-nombre').value = data.nombre;
            document.getElementById('f-apellido').value = data.apellido;
            document.getElementById('f-username').value = data.username;
            document.getElementById('f-email').value = data.email;
            document.getElementById('f-telefono').value = data.telefono || '';
            document.getElementById('f-estado').value = data.estado;
            document.getElementById('f-rol').value = data.rolId;
            document.getElementById('pw-required').style.display = 'none';
        }
    }

    function closeModal() {
        document.getElementById('modal').classList.add('hidden');
    }

    function clearForm() {
        ['f-nombre', 'f-apellido', 'f-username', 'f-email', 'f-telefono', 'f-password'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('f-estado').value = 'Activo';
        document.getElementById('pw-required').style.display = '';
        document.querySelectorAll('.err').forEach(e => e.classList.add('hidden'));
        ['s1', 's2', 's3', 's4'].forEach(id => document.getElementById(id).style.background = '#e2e8f0');
    }

    document.getElementById('toggle-pw').addEventListener('click', function() {
        const pw = document.getElementById('f-password');
        const icon = this.querySelector('i');
        pw.type = pw.type === 'password' ? 'text' : 'password';
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });

    function checkPwStrength() {
        const pw = document.getElementById('f-password').value;
        const colors = ['#ef4444', '#f59e0b', '#3b82f6', '#10b981'];
        let score = 0;
        if (pw.length >= 8) score++;
        if (/[A-Z]/.test(pw)) score++;
        if (/[a-z]/.test(pw)) score++;
        if (/\d/.test(pw)) score++;
        ['s1', 's2', 's3', 's4'].forEach((id, i) => {
            document.getElementById(id).style.background = i < score ? colors[score - 1] : '#e2e8f0';
        });
    }

    function submitForm(e) {
        e.preventDefault();
        let valid = true;
        document.querySelectorAll('.err').forEach(el => el.classList.add('hidden'));
        const isCreate = document.getElementById('form-method').value === '';
        const required = ['f-nombre', 'f-apellido', 'f-username', 'f-email'];
        if (isCreate) required.push('f-password');
        required.forEach(id => {
            const el = document.getElementById(id);
            if (!el.value.trim()) {
                el.style.borderColor = '#ef4444';
                document.querySelector(`.err[data-for="${id}"]`)?.classList.remove('hidden');
                valid = false;
            } else {
                el.style.borderColor = '';
            }
        });
        const pw = document.getElementById('f-password').value;
        if (isCreate && pw && !/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}/.test(pw)) {
            document.querySelector('.err[data-for="f-password"]')?.classList.remove('hidden');
            valid = false;
        }
        if (valid) document.getElementById('user-form').submit();
    }

    function toggleEstado(wrapper, userId) {
        const toggle = wrapper.querySelector('.toggle');
        const label = wrapper.querySelector('.estado-label');
        const isOn = toggle.classList.contains('on');
        const nuevoEstado = isOn ? 'Inactivo' : 'Activo';

        toggle.className = 'toggle ' + (isOn ? 'off' : 'on');
        label.textContent = nuevoEstado;

        fetch(`${BASE_URL}/${userId}/toggle-estado`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                estado: nuevoEstado
            }),
        });
    }

    function showToast(msg, type = 'success') {
        Swal.fire({
            icon: type,
            title: msg,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true
        });
    }

    document.getElementById('modal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    document.querySelectorAll('#user-form input, #user-form select').forEach(el => {
        el.addEventListener('focus', () => {
            el.style.borderColor = '#3b82f6';
            el.style.boxShadow = '0 0 0 3px rgba(59,130,246,.15)';
        });
        el.addEventListener('blur', () => {
            el.style.borderColor = '';
            el.style.boxShadow = '';
        });
    });

    @if ($errors->any())
    document.addEventListener('DOMContentLoaded', () => openModal('create'));
    @endif
</script>
@endpush