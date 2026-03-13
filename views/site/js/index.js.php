(() => {
  const data = window.dashboardChartData || {};
  const chartStore = window.dashboardCharts ?? {};
  window.dashboardCharts = chartStore;

  const destroyChart = (key) => {
    if (chartStore[key]) {
      chartStore[key].destroy();
      delete chartStore[key];
    }
  };

  const currencyFormatter = new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  });

  const numberFormatter = new Intl.NumberFormat('pt-BR', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 3,
  });

  const sharedOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        labels: {
          usePointStyle: true,
          boxWidth: 10,
        },
      },
    },
  };

  const initDashboardCharts = () => {
    if (typeof Chart === 'undefined') {
      return;
    }

    destroyChart('menorEstoque');
    destroyChart('movimentacoes');
    destroyChart('distribuicao');

    const menorEstoqueCanvas = document.getElementById('chart-menor-estoque');
    if (menorEstoqueCanvas && data.menorEstoque) {
      chartStore.menorEstoque = new Chart(menorEstoqueCanvas, {
        type: 'bar',
        data: {
          labels: data.menorEstoque.labels,
          datasets: [{
            label: 'Quantidade atual',
            data: data.menorEstoque.quantidades,
            backgroundColor: 'rgba(194, 107, 73, 0.75)',
            borderColor: 'rgba(194, 107, 73, 1)',
            borderWidth: 1,
            borderRadius: 8,
          }],
        },
        options: {
          ...sharedOptions,
          indexAxis: 'y',
          scales: {
            x: {
              ticks: {
                callback: (value) => numberFormatter.format(value),
              },
              grid: {
                color: 'rgba(146, 111, 83, 0.08)',
              },
            },
            y: {
              grid: {
                display: false,
              },
            },
          },
          plugins: {
            ...sharedOptions.plugins,
            tooltip: {
              callbacks: {
                label: (context) => {
                  const formatted = data.menorEstoque.quantidadesFormatadas?.[context.dataIndex];
                  return formatted ? `Quantidade: ${formatted}` : `Quantidade: ${numberFormatter.format(context.parsed.x)}`;
                },
              },
            },
          },
        },
      });
    }

    const movimentacoesCanvas = document.getElementById('chart-movimentacoes');
    if (movimentacoesCanvas && data.movimentacoes) {
      chartStore.movimentacoes = new Chart(movimentacoesCanvas, {
        type: 'line',
        data: {
          labels: data.movimentacoes.labels,
          datasets: [
            {
              label: 'Entradas',
              data: data.movimentacoes.entradas,
              borderColor: '#198754',
              backgroundColor: 'rgba(25, 135, 84, 0.18)',
              fill: true,
              tension: 0.35,
              pointRadius: 3,
            },
            {
              label: 'Saídas',
              data: data.movimentacoes.saidas,
              borderColor: '#dc3545',
              backgroundColor: 'rgba(220, 53, 69, 0.14)',
              fill: true,
              tension: 0.35,
              pointRadius: 3,
            },
          ],
        },
        options: {
          ...sharedOptions,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: (value) => numberFormatter.format(value),
              },
              grid: {
                color: 'rgba(146, 111, 83, 0.08)',
              },
            },
            x: {
              grid: {
                display: false,
              },
            },
          },
        },
      });
    }

    const distribuicaoCanvas = document.getElementById('chart-distribuicao');
    if (distribuicaoCanvas && data.distribuicao) {
      chartStore.distribuicao = new Chart(distribuicaoCanvas, {
        type: 'doughnut',
        data: {
          labels: data.distribuicao.labels,
          datasets: [{
            data: data.distribuicao.valores,
            backgroundColor: [
              '#7f5539',
              '#b08968',
              '#ddb892',
              '#6d597a',
              '#355070',
              '#588157',
              '#bc6c25',
              '#8d99ae',
              '#adb5bd',
            ],
            borderWidth: 0,
          }],
        },
        options: {
          ...sharedOptions,
          cutout: '62%',
          plugins: {
            ...sharedOptions.plugins,
            tooltip: {
              callbacks: {
                label: (context) => `${context.label}: ${currencyFormatter.format(context.parsed)}`,
              },
            },
          },
        },
      });
    }
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDashboardCharts);
  } else {
    initDashboardCharts();
  }

  $(document).on('pjax:end', initDashboardCharts);
})();
