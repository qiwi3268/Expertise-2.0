import { Field } from './Field';
import { mQS } from '../../lib/main';

/**
 * Представляет собой поле справочника формы
 */
export class MiscField extends Field {

   /**
    * Устанавливает значение поля
    *
    * @param value - id элемента из справочника
    */
   public setValue(value: string): void {
      this.resultInput.value = value;
      const selectedItemLabel: HTMLElement = mQS(`[data-misc-item][data-id="${value}"]`, this.element);
      const fieldLabel: HTMLElement = mQS('[data-field-label]', this.element);
      fieldLabel.textContent = selectedItemLabel.innerHTML;
   }
}
