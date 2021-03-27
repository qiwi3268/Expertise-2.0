import { Field } from './Field';
import { LogicError } from '../../lib/LogicError';

/**
 * Представляет собой файловое поле формы
 */
export class FileField extends Field {

   /**
    * Выводит ошибку при установке значения в файловое поле
    */
   public setValue(value: string): void {
      new LogicError('Установка значения в файловое поле недоступна');
   }

   /**
    * Определяет, содержит ли поле значение
    */
   public hasValue(): boolean {
      return this.resultInput.value !== '';
   }

   /**
    * Выводит ошибку при получении значения файлового поля
    */
   public getValue(): string {
      new LogicError('Получение значения файлового поля недоступно');
      return '';
   }

   // todo загружены невалидные файлы


}
