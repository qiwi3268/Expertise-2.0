import { Field } from './Field';
import { mQS } from '../../lib/main';

/**
 * Представляет собой поле формы с датой
 */
export class DateField extends Field {

   // todo добавить паттерн

   /**
    * Получает значение поля
    */
   public getValue(): string {
      return this.resultInput.value;
   }

   /**
    * Определяет, содержит ли поле значение
    */
   public hasValue(): boolean {
      return this.resultInput.value !== '';
   }

   /**
    * Устанавливает значение поля
    *
    * @param value - новое значение
    */
   public setValue(value: string): void {
      this.resultInput.value = value;
      const fieldLabel: HTMLElement = mQS('[data-field-label]', this.element);
      fieldLabel.textContent = this.resultInput.value;
   }
}
