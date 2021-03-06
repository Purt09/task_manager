<?php

namespace app\modules\project\models;

use app\modules\user\models\chat\Chat;
use Yii;
use app\modules\task\models\Task;
use app\modules\user\models\User;
use app\modules\project\Module;
use app\modules\user\models\connections\ProjectUser;

/**
 * This is the model class for table "{{%project}}".
 *
 * @property int $id
 * @property int $time_at
 * @property string $title
 * @property string $description
 * @property int $parent_id
 * @property int $creator_id
 * @property int $chat_id
 * @property Project $parent
 * @property Project[] $projects
 * @property Task[] $tasks
 */
class Project extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%project}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [ 'title', 'required'],
            [['time_at', 'parent_id', 'creator_id', 'chat_id'], 'integer'],
            [['title', 'description'], 'string', 'max' => 255],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'time_at' => Module::t('module', 'TIME_END_AT'),
            'title' => Module::t('module', 'PROJECT_TITLE'),
            'description' => Module::t('module', 'PROJECT_DESCRIPTION'),
            'parent_id' => Module::t('module', 'PROJECT_PARENT'),
            'creator_id' => 'Создал',
        ];
    }

    /**
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function setLeader(User $user, Project $project){
        $projects = $project->getSubprojectsByProject($project);
        $projects += array(
            $project->id => $project,
        );
        foreach ($projects as $p) {
            $p->creator_id = $user->id;
            $p->save();
        }
    }


    /**
     * Добавляет пользователя в проект и во все подпроекты этого проекта
     *
     * @param User $user
     * @param Project $project
     *
     */
    public function setUserInProjects(User $user, Project $project){
        $task = new Task();
        $projects = $project->getSubprojectsByProject($project);
        $projects += array(
            $project->id => $project,
        );
        // Связываем нового пользователя и проекты
        foreach ($projects as $p)
            $p->link('users', $user);

        $task->setUserInTasks($user, $projects);
    }


    /**
     * Удаляет пользователя из проекта и всех его подпроектов жтого $project
     *
     * @param User $user
     * @param Project $project
     */
    public function delUser(User $user, Project $project){
        $task = new Task();
        $projects = $project->getSubprojectsByProject($project);
        $projects += array(
            $project->id => $project,
        );
        foreach ($projects as $p)
            ProjectUser::deleteAll(['project_id' => $p->id, 'user_id' => $user->id]);

        $task->delUser($projects, $user);
    }



    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        if (Yii::$app->request->post()) {

            $user = User::findOne(Yii::$app->user->identity->id);
            $project = Project::find()->where(['id' => $this->id])->one();
            $project->link('users', $user);

            // при создание подпроекта необходимо привязать всех участников к этому проекту
            if($project->parent_id != null) {
                $userProjects = ProjectUser::find()->where(['project_id' => $project->parent_id])->indexBy(['user_id'])->all();

                $userIds = array_keys($userProjects);
                // Удаляет пользователя, который уже создал проект(для избежания дублей в бд
                unset($userIds[array_search(Yii::$app->user->identity->id, $userIds)]);

                $users = User::find()->where(['in', 'id', $userIds])->all();
                foreach ($users as $u)
                    $project->link('users', $u);
            }
        }
        return parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        // Сохраняем создателя
        if ($this->creator_id == null)
            $this->creator_id = Yii::$app->user->identity->id;

        // У подпроекта чат родительского проекта
        if($this->parent_id != null) {
            $parent = Project::findOne($this->parent_id);
            $this->chat_id = $parent->chat_id;
        } else {
            // Создание чата
            $chat = new Chat();
            $chat->created_at = time();
            $chat->name = $this->title;
            $chat->save();
            $chat->addMessage('Чат создан пользователем: ' . Yii::$app->user->identity->username);
            $this->chat_id = $chat->id;
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }


    /**
     * Возвращает все подпроекты без самого $project
     *
     * @param Project $project
     * @return Project[]|array
     */
    public function getSubprojectsByProject(Project $project){
        return Project::find()->where(['parent_id' => $project->id])->indexBy('id')->all();
    }


    /**
     * Получить родительскую категорию
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Project::className(), ['id' => 'parent_id']);
    }

    /**
     * Получить дочерние категории
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjects()
    {
        return $this->hasMany(Project::className(), ['parent_id' => 'id']);
    }

    /**
     * Получить задачи категории
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::className(), ['project_id' => 'id']);
    }

    /**
     * Получить чат категории
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChat()
    {
        return $this->hasOne(Chat::className(), ['id' => 'chat_id']);
    }

    /**
     * {@inheritdoc}
     * @return ProjectQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProjectQuery(get_called_class());
    }



    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])
            ->viaTable('{{%user_project}}', ['project_id' => 'id']);
    }



}
