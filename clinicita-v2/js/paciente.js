// PACIENTE — Reserva de cita, Filtrar por especialidad, médico y fecha,
// Mostrar horarios disponibles, Enviar formulario al gateway
const pacienteApp = (() => {
  const selectEspecialidad = document.getElementById('filtro-especialidad');
  const selectMedico       = document.getElementById('filtro-medico');
  const selectFecha        = document.getElementById('filtro-fecha');
  const horarioIdInput     = document.getElementById('horario_id');
  const grid               = document.getElementById('horarios-grid');
  let selectedButton = null;

  function formatDateLabel(value) {
    const [anio, mes, dia] = value.split('-').map(Number);
    const diasSemana  = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
    const mesesNombre = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
    const fecha = new Date(anio, mes - 1, dia, 12, 0, 0);
    return diasSemana[fecha.getDay()] + ' ' + dia + ' ' + mesesNombre[mes - 1];
  }

  function convertTo12Hour(time24) {
    const [hours, minutes] = time24.split(':');
    let hours12 = parseInt(hours);
    const ampm = hours12 >= 12 ? 'p.m' : 'a.m';
    if (hours12 > 12) hours12 -= 12;
    if (hours12 === 0) hours12 = 12;
    return `${String(hours12).padStart(2, '0')}:${minutes} ${ampm}`;
  }

  function slotClase(h) {
    if (!h.disponible || h.cita_estado === 'confirmada' || h.cita_estado === 'realizada')
      return 'slot-ocupado';
    if (h.cita_estado === 'pendiente') return 'slot-pendiente';
    return 'slot-disponible';
  }

  function loadMedicos() {
    const especialidad = selectEspecialidad.value;
    const medicos = window.horariosData.medicos
      .filter(m => !especialidad || m.especialidad === especialidad)
      .filter(m => window.horariosData.horarios.some(h => Number(h.medico_id) === Number(m.id)));

    if (!medicos.length) {
      selectMedico.innerHTML = '<option value="">No hay médicos con horarios</option>';
      renderFechas(); renderHorarios(); return;
    }
    selectMedico.innerHTML = [
      '<option value="">Selecciona un médico</option>',
      ...medicos.map(m => `<option value="${m.id}">${m.nombre} — ${m.especialidad}</option>`)
    ].join('');
    renderFechas(); renderHorarios();
  }

  function renderFechas() {
    const medicoId = Number(selectMedico.value);
    const fechas = [...new Set(
      window.horariosData.horarios
        .filter(h => Number(h.medico_id) === medicoId)
        .map(h => h.fecha)
    )];
    horarioIdInput.value = ''; selectedButton = null;
    if (!medicoId) { selectFecha.innerHTML = '<option value="">Selecciona primero un médico</option>'; return; }
    if (!fechas.length) { selectFecha.innerHTML = '<option value="">No hay fechas disponibles</option>'; return; }
    selectFecha.innerHTML = [
      '<option value="">Selecciona una fecha</option>',
      ...fechas.map(f => `<option value="${f}">${formatDateLabel(f)}</option>`)
    ].join('');
  }

  function renderHorarios() {
    const medicoId = Number(selectMedico.value);
    const fecha = selectFecha.value;
    horarioIdInput.value = ''; selectedButton = null;
    if (!medicoId || !fecha) {
      grid.innerHTML = '<p class="nota">Selecciona un médico y una fecha para ver los horarios.</p>'; return;
    }
    const horarios = window.horariosData.horarios.filter(
      h => Number(h.medico_id) === medicoId && h.fecha === fecha
    );
    if (!horarios.length) {
      grid.innerHTML = '<p class="nota">No hay horarios agendados para este médico en la fecha seleccionada.</p>'; return;
    }
    grid.innerHTML = horarios.map(h => {
      const clase    = slotClase(h);
      const disabled = clase !== 'slot-disponible' ? 'disabled' : '';
      return `<button type="button" class="slot ${clase}" data-id="${h.id}" ${disabled}>${convertTo12Hour(h.hora)}</button>`;
    }).join('');
    grid.querySelectorAll('.slot:not([disabled])').forEach(button => {
      button.addEventListener('click', () => {
        if (selectedButton) { selectedButton.classList.remove('slot-selected'); selectedButton.classList.add('slot-disponible'); }
        button.classList.remove('slot-disponible'); button.classList.add('slot-selected');
        selectedButton = button; horarioIdInput.value = button.dataset.id;
      });
    });
  }

  // Actualiza slots desde el gateway (svc=schedules&action=estado)
  function actualizarSlots() {
    const medicoId = Number(selectMedico.value);
    const fecha = selectFecha.value;
    if (!medicoId || !fecha) return;
    fetch(`gateway.php?svc=schedules&action=estado&medico_id=${medicoId}&fecha=${fecha}`)
      .then(r => r.json())
      .then(data => {
        if (!Array.isArray(data)) return;
        data.forEach(h => {
          const btn = grid.querySelector(`.slot[data-id="${h.id}"]`);
          if (!btn) return;
          const nuevaClase = slotClase(h);
          if (btn === selectedButton) {
            if (nuevaClase !== 'slot-disponible') {
              selectedButton = null; horarioIdInput.value = '';
              btn.className = `slot ${nuevaClase}`; btn.disabled = true;
            }
          } else {
            btn.className = `slot ${nuevaClase}`;
            btn.disabled  = nuevaClase !== 'slot-disponible';
          }
        });
      })
      .catch(() => {});
  }

  function init() {
    if (!selectEspecialidad || !selectMedico || !selectFecha || !grid) return;
    selectEspecialidad.addEventListener('change', loadMedicos);
    selectMedico.addEventListener('change', () => { renderFechas(); renderHorarios(); });
    selectFecha.addEventListener('change', renderHorarios);
    loadMedicos();
    setInterval(actualizarSlots, 3000);
  }

  return { init };
})();

// Envío del formulario de cita al gateway
window.addEventListener('DOMContentLoaded', () => {
  document.getElementById('formCita').addEventListener('submit', e => {
    e.preventDefault();
    const horarioId = document.getElementById('horario_id').value;
    if (!horarioId) {
      Modal.warning('Debes seleccionar un horario disponible antes de continuar.', 'Selecciona un horario');
      return;
    }
    const motivo = document.querySelector('[name="motivo"]').value;
    fetch('gateway.php?svc=appointments&action=crear', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `horario_id=${horarioId}&motivo=${encodeURIComponent(motivo)}`
    })
    .then(r => r.json())
    .then(data => {
      if (data.redirect) { location.href = data.redirect; return; }
      if (data.ok) {
        Modal.success('¡Tu cita ha sido confirmada exitosamente!', 'Cita confirmada')
          .then(() => { location.href = 'paciente.php#mis-citas'; });
      } else {
        const btn = document.querySelector(`.slot[data-id="${data.horario_id}"]`);
        if (btn) { btn.className = 'slot slot-ocupado'; btn.disabled = true; }
        document.getElementById('horario_id').value = '';
        Modal.warning('Ese horario acaba de ser tomado por otro paciente. Por favor selecciona otro.', 'Horario no disponible');
      }
    })
    .catch(() => Modal.error('Error de conexión. Intenta de nuevo.'));
  });
});
