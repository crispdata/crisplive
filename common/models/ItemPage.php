<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Project model
 *
 * @property integer $ProjectId
 * @property integer $ContactId
 * @property integer $TemplateId
 * @property string $EventName
 * @property string $EventLocation
 * @property string $EventLatitude
 * @property string $EventLongtitude
 * @property integer $ClientId
 * @property date $EventStartDate
 * @property integer $EventEndDate
 * @property integer $IsFromTemplate
 * @property integer $IsDuplicated
 * @property integer $DuplicatedFromProject
 * @property integer $CreatedOn
 * @property integer $IsActive
 */
class ItemPage extends ActiveRecord {
    //const STATUS_DELETED = '0';
    //const STATUS_ACTIVE = '10';

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%itemspages}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
        ];
    }

}
