(() => {
  const tabela = document.getElementById('tabela-ingredientes')?.querySelector('tbody');
  const btnAdd = document.getElementById('btn-add-ingrediente');
  const template = document.getElementById('receita-ingrediente-row-template');

  if (!tabela || !btnAdd || !template) return;

  btnAdd.addEventListener('click', () => {
    const tr = document.createElement('tr');
    tr.innerHTML = template.innerHTML.trim();
    tabela.appendChild(tr);
  });

  tabela.addEventListener('click', (event) => {
    if (!event.target.classList.contains('btn-remover')) return;

    const linha = event.target.closest('tr');
    if (!linha) return;

    if (tabela.querySelectorAll('tr').length > 1) {
      linha.remove();
      return;
    }

    linha.querySelectorAll('input, select').forEach((el) => {
      el.value = '';
    });
  });
})();
