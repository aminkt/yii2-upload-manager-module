<?php
namespace aminkt\uploadManager\models;

use aminkt\uploadManager\UploadManager;
use yii\data\ActiveDataProvider;

class FileSearch extends UploadmanagerFiles
{
    public $fileName;

    public $filterCreateAt;
    public $filterUpdateAt;

    public function afterValidate()
    {
        if ($this->createTime) {
            $this->filterCreateAt = [
                self::convertJalaliDateToSqlDateTime($this->createTime),
                self::convertJalaliDateToSqlDateTime($this->createTime . ' 23:59:59'),
            ];
        }
        if ($this->updateTime) {
            $this->filterUpdateAt = [
                self::convertJalaliDateToSqlDateTime($this->updateTime),
                self::convertJalaliDateToSqlDateTime($this->updateTime . ' 23:59:59'),
            ];
        }

        parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'userId', 'fileType', 'status'], 'integer'],
            [['name', 'description', 'file', 'fileName'], 'string'],
            [['updateTime', 'createTime'], 'safe']
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
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'fileType' => $this->fileType,
            'status' => $this->status,
        ]);

        if(!UploadManager::getInstance()->adminId or !in_array(\Yii::$app->getUser()->getId(), UploadManager::getInstance()->adminId)){
            $query->andFilterWhere([
                'userId' => \Yii::$app->getUser()->getId(),
            ]);
        } elseif($this->userId and in_array(\Yii::$app->getUser()->getId(), UploadManager::getInstance()->adminId)){
            $query->andFilterWhere([
                'userId' => $this->userId,
            ]);
        }


        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'file', $this->file]);

        if($this->fileName){
            $query->andFilterWhere(['like', 'metaData', '"name":"'.$this->fileName]);

        }

        if ($this->updateTime) {
            $query->andFilterWhere(['between', 'updateTime', $this->filterUpdateAt[0], $this->filterUpdateAt[1]]);
        }

        if ($this->createTime) {
            $query->andFilterWhere(['between', 'createTime', $this->filterCreateAt[0], $this->filterCreateAt[1]]);
        }

        return $dataProvider;
    }

    /**
     * Convert jalali date to Sql Date time.
     *
     * @param string $date
     *
     * @author  Amin Keshavarz <amin@keshavarz.pro>
     */
    public static function convertJalaliDateToSqlDateTime($date)
    {
        $dateTime = explode(' ', $date);

        $date = \IntlCalendar::createInstance(
            'Asia/Tehran',
            'fa_IR@calendar=persian'
        );

        $datePart = explode('-', $dateTime[0]);
        if (count($datePart) != 3) {
            throw new \InvalidArgumentException("Date part should be like 1396-1-22");
        }
        if (isset($dateTime[1])) {
            $timePart = explode(':', $dateTime[1]);
            if (count($datePart) != 3) {
                throw new \InvalidArgumentException("Time part should be like 11:22:33");
            }
            $datePart[3] = ($timePart[0]);
            $datePart[4] = ($timePart[1]);
            $datePart[5] = ($timePart[2]);
        } else {
            $datePart[3] = 0;
            $datePart[4] = 0;
            $datePart[5] = 0;
        }

        if (count($datePart) != 6) {
            throw new \InvalidArgumentException("Date format should be like 1396-1-8 11:22:33");
        }

        $date->set(intval($datePart[0]), intval($datePart[1] - 1), intval($datePart[2]),
            intval($datePart[3]), intval($datePart[4]), intval($datePart[5]));

        $formatter = \Yii::$app->getFormatter();
        $formatter->calendar = \IntlDateFormatter::GREGORIAN;
        $formatter->locale = "en_US@calendar=gregorian";
        $date = \Yii::$app->getFormatter()->asDatetime($date->toDateTime(), 'yyyy-MM-dd HH:mm:ss');
        $formatter->calendar = \IntlDateFormatter::TRADITIONAL;
        $formatter->locale = "fa_IR@calendar=persian";
        return $date;
    }
}