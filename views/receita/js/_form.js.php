(() => {
  const tabelaWrapper = document.getElementById('tabela-ingredientes');
  const tabela = tabelaWrapper?.querySelector('tbody');
  const btnAdd = document.getElementById('btn-add-ingrediente');
  const template = document.getElementById('receita-ingrediente-row-template');
  const unidadeUrl = tabelaWrapper?.dataset.unidadeUrl;

  if (!tabelaWrapper || !tabela || !btnAdd || !template || !unidadeUrl) return;

  const limparUnidade = (linha) => {
    const unidadeId = linha.querySelector('.js-unidade-id');
    const unidadeDisplay = linha.querySelector('.js-unidade-display');

    if (unidadeId) unidadeId.value = '';
    if (unidadeDisplay) unidadeDisplay.value = '';
  };

  const preencherUnidade = (linha, data) => {
    const unidadeId = linha.querySelector('.js-unidade-id');
    const unidadeDisplay = linha.querySelector('.js-unidade-display');

    if (unidadeId) unidadeId.value = data.unidade_medida_id || '';
    if (unidadeDisplay) unidadeDisplay.value = data.unidade || '';
  };

  const carregarUnidade = (select) => {
    const linha = select.closest('tr');
    if (!linha) return;

    if (!select.value) {
      limparUnidade(linha);
      return;
    }

    $.getJSON(unidadeUrl, { id: select.value })
      .done((data) => preencherUnidade(linha, data || {}))
      .fail(() => limparUnidade(linha));
  };

  btnAdd.addEventListener('click', () => {
    const tr = document.createElement('tr');
    tr.innerHTML = template.innerHTML.trim();
    tabela.appendChild(tr);
  });

  tabela.addEventListener('change', (event) => {
    if (!event.target.classList.contains('js-ingrediente-select')) return;
    carregarUnidade(event.target);
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

  tabela.querySelectorAll('.js-ingrediente-select').forEach((select) => {
    if (select.value) {
      carregarUnidade(select);
      return;
    }

    limparUnidade(select.closest('tr'));
  });
})();
