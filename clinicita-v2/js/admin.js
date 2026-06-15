// ADMIN — Navegación y gestión de médicos, Cambio de secciones del panel, Modal de editar médico, Eliminar médico, Filtros de citas y cupos

// NAVEGACIÓN ENTRE SECCIONES
document.querySelectorAll('.nav-link').forEach(link => {
  link.addEventListener('click', e => {
    e.preventDefault();
    const target = link.dataset.seccion;
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('activo'));
    document.querySelectorAll('.seccion').forEach(s => s.classList.remove('activa'));
    link.classList.add('activo');
    document.getElementById('seccion-' + target).classList.add('activa');
  });
});

/* EDITAR MÉDICO*/
const modalEditar = document.getElementById('modal-editar');

function abrirEditar(id, nombre, especialidad, email) {
  document.getElementById('edit-id').value          = id;
  document.getElementById('edit-nombre').value      = nombre;
  document.getElementById('edit-especialidad').value = especialidad;
  document.getElementById('edit-email').value       = email;
  document.getElementById('edit-password').value    = '';
  modalEditar.style.display = 'flex';
}

function cerrarEditar() {
  modalEditar.style.display = 'none';
}

// Cerrar modal al hacer clic fuera de la caja //
modalEditar.addEventListener('click', e => {
  if (e.target === modalEditar) cerrarEditar();
});

// ELIMINAR MÉDICO //
function eliminarMedico(id, nombre) {
  if (confirm('¿Seguro que deseas eliminar al médico ' + nombre + '? Esta acción no se puede deshacer.')) {
    const form  = document.createElement('form');
    form.method = 'POST';
    form.action = 'php/eliminar_medico.php';
    const input = document.createElement('input');
    input.type  = 'hidden';
    input.name  = 'id';
    input.value = id;
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
  }
}

//VALIDACIÓN DE FECHAS //
const fechaInicio = document.getElementById('fecha_inicio');
const fechaFin    = document.getElementById('fecha_fin');
if (fechaInicio && fechaFin) {
  fechaInicio.addEventListener('change', () => {
    fechaFin.min = fechaInicio.value;
    if (fechaFin.value && fechaFin.value < fechaInicio.value) {
      fechaFin.value = fechaInicio.value;
    }
  });
}

// CONVERSIÓN DE HORA — 24h a 12h con a.m / p.m //
function convertTo12HourJS(time24) {
  const [h24, min] = time24.split(':');
  let h = parseInt(h24);
  const ampm = h >= 12 ? 'p.m' : 'a.m';
  if (h > 12) h -= 12;
  if (h === 0) h = 12;
  return String(h).padStart(2, '0') + ':' + min + ' ' + ampm;
}

// TABLA DE CITAS — Filtro por médico //
(function() {
  const doctorSelect = document.getElementById('admin-filtro-medico');
  const tableBody    = document.getElementById('admin-citas-body');
  if (!doctorSelect || !tableBody) return;

  function renderCitas() {
    const medicoId = doctorSelect.value;
    const rows = window.adminCitas
      .filter(c => !medicoId || c.medico_id == medicoId)
      .map(c => `<tr>
        <td>${c.fecha}</td>
        <td>${convertTo12HourJS(c.hora)}</td>
        <td>${c.medico}</td>
        <td>${c.especialidad}</td>
        <td>${c.paciente}</td>
        <td>${c.paciente_email}</td>
        <td>${c.estado}</td>
      </tr>`).join('');
    tableBody.innerHTML = rows || '<tr><td colspan="7" style="text-align:center;">No hay citas para ese médico.</td></tr>';
  }

  doctorSelect.addEventListener('change', renderCitas);
  renderCitas();
})();

(function() {
  const cupoSelect = document.getElementById('admin-filtro-cupo');
  const cupoRows   = document.querySelectorAll('#admin-cupos-table tbody tr');
  if (!cupoSelect) return;

  cupoSelect.addEventListener('change', () => {
    const medicoId = cupoSelect.value;
    cupoRows.forEach(row => {
      row.style.display = !medicoId || row.dataset.medicoId === medicoId ? '' : 'none';
    });
  });
})();
