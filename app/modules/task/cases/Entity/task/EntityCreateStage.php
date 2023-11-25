<?php

declare(strict_types=1);

namespace kuaukutsu\poc\demo\modules\task\cases\Entity\task;

use kuaukutsu\ds\dto\DtoInterface;
use kuaukutsu\poc\demo\components\bridge\BridgeRunnable;
use kuaukutsu\poc\demo\components\identity\GuestIdentity;
use kuaukutsu\poc\demo\shared\request\Saga\EntityCreateRequest;
use kuaukutsu\poc\task\state\TaskStateInterface;
use kuaukutsu\poc\task\state\TaskStateMessage;
use kuaukutsu\poc\task\TaskHandlerBase;
use kuaukutsu\poc\task\TaskStageContext;

final class EntityCreateStage extends TaskHandlerBase
{
    /**
     * @param non-empty-string $token
     */
    public function __construct(
        public readonly string $token,
        private readonly BridgeRunnable $bridge,
    ) {
    }

    public function handle(TaskStageContext $context): TaskStateInterface
    {
        /** @var DtoInterface $response */
        $response = $this->bridge
            ->run(
                new EntityCreateRequest(
                    new GuestIdentity($this->token),
                    [
                        'comment' => "[$context->task] task handler.",
                    ],
                    [
                        'php',
                        'task',
                    ]
                )
            );

        $entity = EntityResponse::hydrate(
            $response->toArrayRecursive()
        );

        return $this->success(
            new TaskStateMessage($entity->comment),
            $context,
            $entity,
        );
    }
}
