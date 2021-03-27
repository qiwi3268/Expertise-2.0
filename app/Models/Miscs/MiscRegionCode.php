<?php


namespace App\Models\Miscs;


/**
 * Код региона
 *
 */
final class MiscRegionCode extends MiscModel
{

    /**
     * Аксессор заголовка для отображения в html
     *
     * @return string
     */
    public function getLabelAttribute(): string
    {
        $this->existsAttributes(['name', 'code']);
        return $this->code . ' ' . $this->name;
    }
}
