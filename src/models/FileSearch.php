<?php
namespace aminkt\yii2\uploadmanager\models;

use aminkt\yii2\uploadmanager\UploadManager;
use yii\data\ActiveDataProvider;

class FileSearch extends File
{
    public $fileName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'file_type', 'status'], 'integer'],
            [['name', 'description', 'file', 'file_name'], 'string'],
            [['update_at', 'create_at'], 'each', 'rule' => ['datetime']]
        ];
    }

    /**
     * Search in files.
     *
     * @param $params
     *
     * @return ActiveDataProvider
     */
    public function search($params){
        $query = UploadmanagerFiles::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50
            ],
            'sort' => [
                'defaultOrder' => [
                    'create_at' => SORT_DESC,
                    'update_at' => SORT_DESC,
                ]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'file_type' => $this->fileType,
            'status' => $this->status,
        ]);

        if(!UploadManager::getInstance()->adminId or !in_array(\Yii::$app->getUser()->getId(), UploadManager::getInstance()->adminId)){
            $query->andFilterWhere([
                'user_id' => \Yii::$app->getUser()->getId(),
            ]);
        } elseif($this->userId and in_array(\Yii::$app->getUser()->getId(), UploadManager::getInstance()->adminId)){
            $query->andFilterWhere([
                'user_id' => $this->userId,
            ]);
        }


        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'file', $this->file]);

        if($this->fileName){
            $query->andFilterWhere(['like', 'meta_data', '"name":"'.$this->fileName]);

        }


        // Time filtering
        if (isset($this->update_at[0])) {
            $query->andFilterWhere(['>=', 'update_at', $this->update_at[0]]);
        }

        if (isset($this->update_at[1])) {
            $query->andFilterWhere(['<=', 'update_at', $this->update_at[1]]);
        }

        if (isset($this->create_at[0])) {
            $query->andFilterWhere(['>=', 'create_at', $this->create_at[0]]);
        }

        if (isset($this->create_at[1])) {
            $query->andFilterWhere(['<=', 'create_at', $this->create_at[1]]);
        }

        return $dataProvider;
    }
}