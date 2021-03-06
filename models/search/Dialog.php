<?php

namespace rgen3\tickets\models\search;

use rgen3\tickets\models\TicketMessage;
use rgen3\tickets\models\TicketTheme;
use rgen3\tickets\Module;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

class Dialog extends ActiveRecord
{
    public $themeId;
    public $dateCreated;

    public $withMessages = false;
    private $userId;

    public function init()
    {
        $this->setUserId();
        parent::init(); // TODO: Change the autogenerated stub
    }

    public static function tableName()
    {
        return '{{%ticket_themes}}';
    }

    public function rules()
    {
        return [
            [['id', 'userId', 'themeId'], 'integer'],
            [['withMessages'], 'boolean'],
            [['dateCreated'], 'string']
        ];
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId()
    {
        $this->userId = \Yii::$app->user->id;
    }

    public function getAssignedTo()
    {
        return $this->hasOne(Module::$userModel, ['assigned_to' => 'id']);
    }

    public function search($params)
    {
        $query = TicketTheme::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        $this->load($params);

        if (!$this->validate())
        {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andWhere([
            'user_from' => $this->getUserId()
        ]);

        if ($this->withMessages)
        {
            $query->andWhere([
                'theme_id' => $this->themeId,
            ]);
        }

        return $dataProvider;
    }
}