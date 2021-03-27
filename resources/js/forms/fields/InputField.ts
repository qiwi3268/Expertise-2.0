import { Field } from './Field';
import { getRegExpFromString, safeDataAttrGetter } from '../../lib/main';
import IMask from 'imask';

/**
 * Шаблоны для масок текстовых полей
 */
enum InputMasks {
   '/^[0-9]{4}\\-[0-9]{6}$/' = '0000-000000', //Passport
   '/^[0-9]{3}-[0-9]{3}-[0-9]{3} [0-9]{2}$/' = '000-000-000 00', //Snils
   '/^\\d{9}$/' = '000000000', //Kpp, Bik
   '/^\\d{10}$/' = '0000000000', //OrgInn
   '/^\\d{12}$/' = '000000000000', //PersInn
   '/^\\d{13}$/' = '0000000000000', //Ogrn
   '/^\\d{15}$/' = '000000000000000', //Ogrnip
   '/^\\d{6}$/' = '000000', //Postcode
   '/^\\d{36}$/' = '000000000000000000000000000000000000', //Ikz
   '/^\\d{20}$/' = '000000000', //CheckingAccount, CorrespondentAccount
}

/**
 * Регулярные выражения для масок текстовых полей
 */
enum MaskPatterns {
   Name = '/^[а-яё-]*$/iu'
}

/**
 * Регулярные выражения текстовых полей
 */
enum InputPatterns {
   Decimal = '/^(0|-?[1-9][0-9]{0,8})(,[0-9]{1,7})?$/',
   Integer = '/^(0|-?[1-9][0-9]{0,8})$/',
   Email = '/^\\S+@\\S+\\.\\S+$/u',
   Name = '/^[а-яё]{2,}(-[а-яё]+)*$/iu',
   Passport = '/^[0-9]{4}\\-[0-9]{6}$/',
   Snils = '/^[0-9]{3}-[0-9]{3}-[0-9]{3} [0-9]{2}$/',
   Kpp = '/^\\d{9}$/',
   OrgInn = '/^\\d{10}$/',
   PersInn = '/^\\d{12}$/',
   Ogrn = '/^\\d{13}$/',
   Ogrnip = '/^\\d{15}$/',
   Postcode = '/^\\d{6}$/',
   Percent = '/^(0|[1-9][0-9]?|100)$/',
   Ikz = '/^\\d{36}$/',
   Bik = '/^\\d{9}$/',
   CheckingAccount = '/^\\d{20}$/',
   CorrespondentAccount = '/^\\d{20}$/',
}

/**
 * Описывает опции для создания маски
 */
type MaskOptions = {
   mask: InputMasks | RegExp | NumberConstructor | StringConstructor,
   [option: string]: any
}

/**
 * Представляет собой текстовое поле формы
 */
export class InputField extends Field {

   /**
    * Строка с регулярное выражением для проверки
    */
   protected readonly pattern: string | undefined;

   /**
    * Сообщение об ошибке
    */
   protected  errorMessage: string;

   /**
    * Регулярное выражение для проверки
    */
   protected regexp: RegExp;

   /**
    * Маска для ввода
    */
   protected mask: IMask.InputMask<any>;

   /**
    * Максимальная длина поля
    */
   protected maxLength: number;

   public constructor(element: HTMLElement) {
      super(element);

      this.pattern = this.element.dataset.pattern;
      this.maxLength = parseInt(safeDataAttrGetter('maxLength', this.element));

      this.initMask();
   }

   /**
    * Инициализирует маску поля
    */
   protected initMask(): void {

      this.errorMessage = 'Поле обязательно для заполнения';

      switch (this.pattern) {
         case InputPatterns.Decimal:
            this.initPatternField(
               this.getDecimalMaskOptions(),
               InputPatterns.Decimal,
               'Значение должно быть десятичным числом меньше 1000000000'
            );
            break;
         case InputPatterns.Integer:
            this.resultInput.setAttribute('maxlength', this.maxLength.toString());
            this.initMaskedFixedField(this.getIntegerMaskOptions(), InputPatterns.Integer);
            this.errorMessage = 'Значение должно быть целым числом меньше 1000000000';
            break;
         case InputPatterns.Email:
            this.initPatternField({mask: String}, InputPatterns.Email, 'Неверный формат электронной почты');
            break;
         case InputPatterns.Name:
            this.initPatternField({mask: getRegExpFromString(MaskPatterns.Name)}, InputPatterns.Name, 'Неверный формат имени');
            break;
         case InputPatterns.Percent:
            this.initMaskedFixedField(this.getPercentMaskOptions(), InputPatterns.Percent);
            break;
         case InputPatterns.Passport:
         case InputPatterns.Snils:
         case InputPatterns.Kpp:
         case InputPatterns.OrgInn:
         case InputPatterns.PersInn:
         case InputPatterns.Ogrn:
         case InputPatterns.Ogrnip:
         case InputPatterns.Postcode:
         case InputPatterns.Ikz:
         case InputPatterns.Bik:
         case InputPatterns.CheckingAccount:
         case InputPatterns.CorrespondentAccount:
            this.initMaskedFixedField({mask: InputMasks[this.pattern]}, this.pattern);
            break;
         default:
            this.resultInput.setAttribute('maxlength', this.maxLength.toString());
            this.mask = IMask(this.resultInput, {mask: String});
            this.regexp = new RegExp('');
            this.mask.on('complete', () => this.complete());
      }

      this.resultInput.addEventListener('focus', () => {
         this.mask.updateOptions({lazy: false});
      });

      this.resultInput.addEventListener('blur', () => {
         this.mask.updateOptions({lazy: true});
      });

   }

   /**
    * Инициализирует маску с регулярным выражением
    *
    * @param maskOptions - опции маски
    * @param pattern - регулярное выражение для проверки
    * @param errorMessage - сообщение с ошибкой
    */
   protected initPatternField(maskOptions: MaskOptions, pattern: InputPatterns, errorMessage: string): void {
      this.resultInput.setAttribute('maxlength', this.maxLength.toString());
      this.mask = IMask(this.resultInput, maskOptions as any);
      this.regexp = getRegExpFromString(pattern);

      this.mask.on('complete', () => {
         if (!this.hasValue() || this.mask.value.match(this.regexp)) {
            this.complete();
         }
      });

      this.errorMessage = errorMessage;
   }

   /**
    * Инициализирует маску с шаблоном
    *
    * @param maskOptions - опции маски
    * @param pattern - регулярное выражение для проверки
    */
   protected initMaskedFixedField(maskOptions: MaskOptions, pattern: InputPatterns): void {
      this.mask = IMask(this.resultInput, maskOptions as any);
      this.regexp = getRegExpFromString(pattern);

      const clearIfEmpty: Function = () => {
         if (!this.hasValue()) {
            this.complete();
            this.mask.off('accept', clearIfEmpty);
         }
      }

      this.mask.on('complete', () => {
         this.complete();
         this.mask.on('accept', clearIfEmpty);
      });

   }

   /**
    * Определяет, является ли поле валидным
    */
   public validate(): boolean {

      if (!this.hasValue()) {
         this.errorElement.textContent = 'Поле обязательно для заполнения';
         this.valid = !this.isRequired();
      } else if (this.getValue().match(this.regexp) === null) {
         this.errorElement.textContent = this.errorMessage;
         this.valid = false;
      } else if (this.getValue().length > this.maxLength) {
         this.errorElement.textContent = 'Превышена максимальная длина поля';
         this.valid = false;
      } else {
         this.valid = true;
      }

      return this.valid;
   }

   /**
    * Устанавливает значение поля
    *
    * @param value - новое значение
    */
   public setValue(value: string) {
      this.mask.unmaskedValue = value;
   }

   /**
    * Определяет, содержит ли поле значение
    */
   public hasValue(): boolean {
      return this.mask.unmaskedValue !== '';
   }

   /**
    * Получает значение поля
    */
   public getValue(): string {
      return this.isComplete() ? this.mask.value.trim() : '';
   }

   /**
    * Определяет заполнена ли маска
    */
   private isComplete(): boolean {
      return this.mask.masked.isComplete;
   }

   /**
    * Получает опции маски для десятичного числа
    */
   protected getDecimalMaskOptions(): MaskOptions {
      return {
         mask: Number,
         scale: 7,
         signed: true,
         normalizeZeros: true,
         radix: ',',
         mapToRadix: ['.']
      };
   }

   /**
    * Получает опции маски для целого числа
    */
   protected getIntegerMaskOptions(): MaskOptions {
      return {
         mask: Number,
         scale: 0,
         signed: true,
      };
   }

   /**
    * Получает опции маски для процента
    */
   protected getPercentMaskOptions(): MaskOptions {
      return {
         mask: Number,
         max: 100,
      };
   }

}
