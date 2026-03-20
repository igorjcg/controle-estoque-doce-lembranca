<?php

namespace app\models\search;

use app\models\Producao;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ProducaoSearch extends Producao
{
    public ?string $receita_nome = null;
    public ?string $criado_por_username = null;

    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['quantidade', 'custo_unitario', 'custo_total'], 'number'],
            [['created_at'], 'safe'],
            [['receita_nome', 'criado_por_username'], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Producao::find()
            ->alias('p')
            ->joinWith(['receita r', 'criadoPor u']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC, 'id' => SORT_DESC],
                'attributes' => [
                    'id',
                    'quantidade',
                    'custo_unitario',
                    'custo_total',
                    'created_at',
                    'receita_nome' => [
                        'asc' => ['r.nome' => SORT_ASC],
                        'desc' => ['r.nome' => SORT_DESC],
                    ],
                    'criado_por_username' => [
                        'asc' => ['u.username' => SORT_ASC],
                        'desc' => ['u.username' => SORT_DESC],
                    ],
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['p.id' => $this->id]);
        $query->andFilterWhere(['p.created_at' => $this->created_at]);
        $query->andFilterWhere(['p.quantidade' => $this->quantidade]);
        $query->andFilterWhere(['p.custo_unitario' => $this->custo_unitario]);
        $query->andFilterWhere(['p.custo_total' => $this->custo_total]);
        $query->andFilterWhere(['like', 'r.nome', $this->receita_nome]);
        $query->andFilterWhere(['like', 'u.username', $this->criado_por_username]);

        return $dataProvider;
    }

    public function attributeLabels(): array
    {
        return array_merge(parent::attributeLabels(), [
            'receita_nome' => 'Receita',
            'criado_por_username' => 'Usuario',
        ]);
    }
}