(() => {
  const overrideYiiConfirm = () => {
    if (typeof yii === 'undefined' || typeof Swal === 'undefined') {
      return;
    }

    yii.confirm = function (message, okCallback, cancelCallback) {
      Swal.fire({
        title: 'Confirmar exclusão',
        text: message || 'Tem certeza que deseja excluir este item?',
        icon: 'warning',
        width: '90%',
        maxWidth: '500px',
        showCancelButton: true,
        confirmButtonText: 'Excluir',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
      }).then((result) => {
        if (result.isConfirmed) {
          if (typeof okCallback === 'function') {
            okCallback();
          }
          return;
        }

        if (typeof cancelCallback === 'function') {
          cancelCallback();
        }
      });
    };
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', overrideYiiConfirm);
  } else {
    overrideYiiConfirm();
  }

  $(document).on('pjax:end', overrideYiiConfirm);
})();
