<?php

declare(strict_types=1);

namespace kuaukutsu\poc\demo\modules\task\service;

use kuaukutsu\poc\task\dto\TaskDto;
use kuaukutsu\poc\task\dto\TaskModel;
use kuaukutsu\poc\task\service\TaskCommand;
use kuaukutsu\poc\task\EntityUuid;
use kuaukutsu\poc\demo\shared\exception\ModelDeleteException;
use kuaukutsu\poc\demo\shared\exception\ModelSaveException;
use kuaukutsu\poc\demo\shared\exception\NotFoundException;
use kuaukutsu\poc\demo\shared\utils\ModelOperationSafely;
use kuaukutsu\poc\demo\shared\entity\pk\PrimaryKeyInterface;
use kuaukutsu\poc\demo\shared\entity\pk\PrimaryUuidCreate;
use kuaukutsu\poc\demo\shared\entity\pk\PrimaryUuidUpdate;
use kuaukutsu\poc\demo\modules\task\models\Task;

final class TaskService implements TaskCommand
{
    use ModelOperationSafely;

    /**
     * @throws ModelSaveException
     */
    public function create(EntityUuid $uuid, TaskModel $model): TaskDto
    {
        return $this->save(
            new PrimaryUuidCreate($uuid->getUuid()),
            $model->toArrayRecursive()
        );
    }

    /**
     * @throws NotFoundException
     * @throws ModelSaveException
     */
    public function update(EntityUuid $uuid, TaskModel $model): TaskDto
    {
        return $this->save(
            new PrimaryUuidUpdate($uuid->getUuid()),
            $model->toArrayRecursive()
        );
    }

    /**
     * @throws NotFoundException
     * @throws ModelSaveException
     */
    public function replace(EntityUuid $uuid, TaskDto $model): bool
    {
        $rows = Task::updateAll(
            [
                'flag' => $model->flag,
                'state' => $model->state,
                'updated_at' => gmdate('c'),
            ],
            $uuid->getQueryCondition(),
        );

        return $rows > 0;
    }

    /**
     * @throws NotFoundException
     * @throws ModelDeleteException
     */
    public function remove(EntityUuid $uuid): bool
    {
        $model = $this->getOne(
            new PrimaryUuidUpdate($uuid->getUuid())
        );

        return $this->deleteSafely($model);
    }

    /**
     * @throws NotFoundException
     * @throws ModelSaveException
     */
    private function save(PrimaryKeyInterface $pk, array $attributes): TaskDto
    {
        $model = $pk->isNewRecord()
            ? new Task($pk->getValue())
            : $this->getOne($pk);

        $model->setAttributes($attributes);
        $this->saveSafely($model);
        $model->refresh();

        return $model->toDto();
    }

    /**
     * @throws NotFoundException
     */
    private function getOne(PrimaryKeyInterface $pk): Task
    {
        return Task::findOne($pk->getValue())
            ?? throw new NotFoundException(
                strtr('[uuid] Task not found.', $pk->getValue())
            );
    }
}