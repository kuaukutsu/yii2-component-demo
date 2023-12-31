<?php

declare(strict_types=1);

namespace kuaukutsu\poc\demo\modules\command\cases\Saga;

use yii\base\Module;
use yii\console\ExitCode;
use kuaukutsu\ds\dto\DtoInterface;
use kuaukutsu\poc\demo\components\identity\GuestIdentity;
use kuaukutsu\poc\demo\components\bridge\BridgeRunnable;
use kuaukutsu\poc\demo\shared\request\Saga\EntityCreateRequest;
use kuaukutsu\poc\demo\modules\command\components\controller\ConsoleCommand;

final class SagaCommand extends ConsoleCommand
{
    public function __construct(
        string $id,
        Module $module,
        private readonly BridgeRunnable $bridge,
        array $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionTest(string $comment): int
    {
        /** @var DtoInterface $response */
        $response = $this->bridge
            ->run(
                new EntityCreateRequest(
                    new GuestIdentity(),
                    [
                        'comment' => $comment,
                    ],
                    [
                        'php',
                        'console',
                        'test',
                        'example',
                        'commit',
                    ]
                )
            );

        if ($this->verbose) {
            $this->stdout(
                var_export($response->toArrayRecursive(), true) . PHP_EOL
            );
        }

        return ExitCode::OK;
    }
}
