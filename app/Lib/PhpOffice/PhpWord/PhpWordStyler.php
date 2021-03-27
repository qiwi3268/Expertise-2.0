<?php

declare(strict_types=1);

namespace App\Lib\PhpOffice\PhpWord;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;


/**
 * Предназначен для инициализации стилей в объект PhpWord
 *
 */
final class PhpWordStyler
{
    public const SECTION_TODO1 = 'section_todo1';
    public const TABLE_TODO1 = 'table_todo1';


    /**
     * Инициализирует стили секции
     *
     * @param PhpWord $word
     */
    public static function initSectionStyles(PhpWord $word): void
    {
        $word->addParagraphStyle(self::SECTION_TODO1, [
            'marginLeft'   => self::cm(2.5),
            'marginRight'  => self::cm(2),
            'marginTop'    => self::cm(2),
            'marginBottom' => self::cm(1.5),
        ]);
    }


    public static function initTableStyles(PhpWord $word): void
    {

    }


    /**
     * Предоставляет короткий синтаксис для конвертации сантиметров в twip
     * (единица измерений в Open Office XML формате)
     *
     * @param float $centimeter
     * @return float
     */
    public static function cm(float $centimeter): float
    {
        return Converter::cmToTwip($centimeter);
    }
}