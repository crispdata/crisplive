<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Report model
 *
 * @property integer $ReportId
 * @property string $ReportTitle
 * @property string $Pages
 * @property string $PagesOrder
 * @property integer $IncludeTimeline
 * @property datetime $EventDateFrom
 * @property datetime $EventDateTo
 * @property datetime $EventRangeFrom
 * @property datetime $EventRangeTo
 * @property string $TimeLineView
 * @property string $ReportFormat
 * @property string $EmailToReport
 * @property string $EmailFromContacts
 * @property string $EmailToOthers
 * @property string $ReportFile
 * @property datetime $CreatedOn
 * @property integer $IsActive
 */
class Report extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%tblreports}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
        ];
    }
}