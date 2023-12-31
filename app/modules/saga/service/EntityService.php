<?php

declare(strict_types=1);

namespace kuaukutsu\poc\demo\modules\saga\service;

use kuaukutsu\poc\demo\shared\utils\ModelOperationSafely;
use kuaukutsu\poc\demo\shared\entity\pk\PrimaryKeyInterface;
use kuaukutsu\poc\demo\shared\entity\pk\PrimaryUuidCreate;
use kuaukutsu\poc\demo\shared\entity\pk\PrimaryUuidUpdate;
use kuaukutsu\poc\demo\shared\exception\ModelDeleteException;
use kuaukutsu\poc\demo\shared\exception\ModelSaveException;
use kuaukutsu\poc\demo\shared\exception\NotFoundException;
use kuaukutsu\poc\demo\modules\saga\models\EntityDto;
use kuaukutsu\poc\demo\modules\saga\models\EntityModel;
use kuaukutsu\poc\demo\modules\saga\models\Entity;

final class EntityService
{
    use ModelOperationSafely;

    /**
     * @param non-empty-string $uuid
     * @throws ModelSaveException
     */
    public function create(string $uuid, EntityModel $dto): EntityDto
    {
        return $this->save(
            new PrimaryUuidCreate($uuid),
            $dto->toArrayRecursive()
        );
    }

    /**
     * @param non-empty-string $uuid
     * @throws NotFoundException
     * @throws ModelSaveException
     */
    public function update(string $uuid, EntityModel $dto): EntityDto
    {
        return $this->save(
            new PrimaryUuidUpdate($uuid),
            $dto->toArrayRecursive()
        );
    }

    /**
     * Use only in rollback.
     *
     * @throws NotFoundException
     * @throws ModelDeleteException
     */
    public function remove(PrimaryKeyInterface $pk): void
    {
        $this->deleteSafely(
            $this->getOne($pk)
        );
    }

    /**
     * @throws NotFoundException
     * @throws ModelSaveException
     */
    private function save(PrimaryKeyInterface $pk, array $attributes): EntityDto
    {
        $model = $pk->isNewRecord()
            ? new Entity($pk->getValue())
            : $this->getOne($pk);

        $model->setAttributes($attributes);
        $this->saveSafely($model);
        $model->refresh();

        return $model->toDto();
    }

    /**
     * @throws NotFoundException
     */
    private function getOne(PrimaryKeyInterface $pk): Entity
    {
        return Entity::findOne($pk->getValue())
            ?? throw new NotFoundException(
                strtr('[uuid] Entity not found.', $pk->getValue())
            );
    }
}
