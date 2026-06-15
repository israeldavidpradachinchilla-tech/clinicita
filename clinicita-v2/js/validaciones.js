// VALIDACIONES — Registro y confirmaciones
// - validarRegistro: revisa el formulario de registro
// - confirmar: pide confirmación antes de acciones críticas
function validarRegistro() {
  const nombre = document.getElementById('nombre').value.trim();
  const cedula = document.getElementById('cedula').value.trim();
  const email  = document.getElementById('email').value.trim();
  const pass   = document.getElementById('password').value;
  if (nombre.length < 3) { alert('Nombre demasiado corto'); return false; }
  if (!/^\d{6,15}$/.test(cedula)) { alert('Cédula inválida (solo números)'); return false; }
  if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) { alert('Correo inválido'); return false; }
  if (pass.length < 6) { alert('La contraseña debe tener al menos 6 caracteres'); return false; }
  return true;
}

function confirmar(msg) { return confirm(msg || '¿Estás seguro?'); }
