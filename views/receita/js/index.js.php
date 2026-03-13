(() => {
  const input = document.getElementById('filtro-receita');
  const rows = () => document.querySelectorAll('#tabela-receitas tbody tr');
  if (!input) return;
  input.addEventListener('input', () => {
    const term = input.value.toLowerCase().trim();
    rows().forEach((row) => {
      const txt = row.innerText.toLowerCase();
      row.style.display = txt.includes(term) ? '' : 'none';
    });
  });

  document.addEventListener('click', async (event) => {
    const button = event.target.closest('.btn-utilizar-receita');
    if (!button) return;

    if (typeof Swal === 'undefined') {
      alert('SweetAlert2 não está disponível.');
      return;
    }

    const receitaId = button.dataset.id;
    const receitaNome = button.dataset.nome;
    const url = button.dataset.url;

    const result = await Swal.fire({
      title: 'Utilizar receita',
      text: `Você deseja utilizar a receita ${receitaNome}?`,
      input: 'number',
      inputLabel: 'Quantidade',
      inputValue: 1,
      inputClass: 'swal-input-quantidade',
      inputAttributes: {
        min: 1,
        step: 1,
      },
      showCancelButton: true,
      confirmButtonText: 'Utilizar',
      cancelButtonText: 'Cancelar',
      preConfirm: (value) => {
        const quantidade = Number(value);
        if (!Number.isFinite(quantidade) || quantidade <= 0) {
          Swal.showValidationMessage('Informe uma quantidade válida');
          return false;
        }

        return quantidade;
      },
    });

    if (!result.isConfirmed) return;

    $.ajax({
      url,
      method: 'POST',
      dataType: 'json',
      data: {
        id: receitaId,
        quantidade: result.value,
      },
    }).done((response) => {
      if (!response || !response.success) {
        Swal.fire('Erro', response?.message || 'Não foi possível utilizar a receita.', 'error');
        return;
      }

      Swal.fire('Sucesso', 'Produção registrada com sucesso.', 'success');
      $.pjax.reload({ container: '#pjax-receitas', async: false });
    }).fail(() => {
      Swal.fire('Erro', 'Não foi possível utilizar a receita.', 'error');
    });
  });
})();
