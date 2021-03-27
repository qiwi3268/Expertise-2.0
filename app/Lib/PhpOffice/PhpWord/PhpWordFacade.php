<?php

declare(strict_types=1);

namespace App\Lib\PhpOffice\PhpWord;


use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Style\Language;
use PhpOffice\PhpWord\Shared\Converter;


final class PhpWordFacade
{
    private PhpWord $word;


    /**
     * Конструктор класса
     *
     */
    public function __construct(bool $initStyles = true)
    {
        $word = new PhpWord();

        $settings = $word->getSettings();

        // Установка десятичного символа
        $settings->setDecimalSymbol(',');
        // Язык документа
        $settings->setThemeFontLang(new Language(Language::RU_RU));

        // Автоматическое экранирования символов
        Settings::setOutputEscapingEnabled(true);

        // Шрифт и его размер
        Settings::setDefaultFontName('Times New Roman');
        Settings::setDefaultFontSize(12);

        if ($initStyles) {

            PhpWordStyler::initSectionStyles($word);
            PhpWordStyler::initTableStyles($word);
        }
        $this->word = $word;
    }



}