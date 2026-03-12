(() => {
  const container = document.querySelector('.ingrediente-form');
  const select = document.querySelector('[name="Ingrediente[unidade_medida_id]"]');
  const label = document.getElementById('label-estoque-minimo');

  if (!container || !select || !label) return;

  const unidadesSiglas = JSON.parse(container.dataset.unidadesSiglas || '{}');

  select.addEventListener('change', function () {
    const sigla = unidadesSiglas[this.value];
    label.textContent = 'Estoque minimo para alerta' + (sigla ? ' (em ' + sigla + ')' : '');
  });
})();
