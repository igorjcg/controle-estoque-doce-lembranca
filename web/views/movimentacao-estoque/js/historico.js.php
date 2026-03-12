(() => {
  const input = document.getElementById('filtro-historico');
  const rows = () => document.querySelectorAll('#tabela-historico tbody tr');
  if (!input) return;
  input.addEventListener('input', () => {
    const term = input.value.toLowerCase().trim();
    rows().forEach((row) => {
      const txt = row.innerText.toLowerCase();
      row.style.display = txt.includes(term) ? '' : 'none';
    });
  });
})();
