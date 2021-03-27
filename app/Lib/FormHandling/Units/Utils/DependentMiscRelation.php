<?php

declare(strict_types=1);

namespace App\Lib\FormHandling\Units\Utils;

use App\Exceptions\Lib\FormHandling\FormLogicException;
use App\Exceptions\Lib\FormHandling\FormInvalidArgumentException;

use App\Models\Miscs\MiscModel;

use App\Lib\FormHandling\Units\Items\Miscs\MiscItem;
use App\Lib\FormHandling\Units\Items\Miscs\SingleMisc;
use App\Lib\FormHandling\Units\Items\Miscs\MultipleMisc;

use App\Lib\Settings\Miscs\DependentMiscsManager;
use App\Repositories\Miscs\MiscRepository;


final class DependentMiscRelation
{


    /**
     * Проверяет существование отношения между двумя справочниками
     *
     * @param SingleMisc $mainMisc
     * @param MiscItem $subMisc
     * @return bool
     * @throws FormLogicException
     */
    public static function existsRelation(
        SingleMisc $mainMisc,
        MiscItem $subMisc
    ): bool {

        $mainAlias = $mainMisc->getAlias();
        $subAlias = $subMisc->getAlias();

        $mgr = DependentMiscsManager::getInstance();

        if (!$mgr->existsByAliases($mainAlias, $subAlias)) {
            throw new FormInvalidArgumentException("Справочники: '{$mainAlias}' и '{$subAlias}' не имеют связь");
        }
        if (!$mainMisc->isFilled() || !$subMisc->isFilled()) {
            throw new FormLogicException("Один из обрабатываемых справочников: '{$mainMisc->getName()}' и/или '{$subMisc->getName()}' незаполнен");
        }

        if ($subMisc instanceof SingleMisc) {

            $subIds = [(int) $subMisc->getValue()];
        } elseif($subMisc instanceof MultipleMisc) {

            $subIds = $subMisc->getSelectedIds();
        } else {

            throw new FormInvalidArgumentException("Неизвестный тип справочника: '{$subAlias}'");
        }

        $obj = $mgr->getObjectByAliases($mainAlias, $subAlias);

        /** @var MiscModel $mainMiscModel */
        $mainMiscModel = new $obj->main['class'];

        $rep = new MiscRepository($mainMiscModel);

        foreach ($subIds as $id) {

            if (!$rep->existsBelongsToManyRelation(
                $mainMiscModel,
                $obj->main['relation'],
                (int) $mainMisc->getValue(),
                $id
            )) {
                return false;
            }
        }
        return true;
    }
}