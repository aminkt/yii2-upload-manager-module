<?php
namespace aminkt\uploadManager\models\mongo;

use aminkt\uploadManager\UploadManager;
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
            [['_id', 'user_id', 'file_type', 'status'], 'integer'],
            [['name', 'description', 'file', 'fileName'], 'string'],
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
        $query = File::find();

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

        if ($this->updateTime) {
            $query->andFilterWhere(['between', 'update_at', $this->filterUpdateAt[0], $this->filterUpdateAt[1]]);
        }

        if ($this->createTime) {
            $query->andFilterWhere(['between', 'create_at', $this->filterCreateAt[0], $this->filterCreateAt[1]]);
        }

        return $dataProvider;
    }
}