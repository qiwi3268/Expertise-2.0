import { Field } from './Field';
import { htmlArrDecode, mQSA, safeDataAttrGetter } from '../../lib/main';
import { removeCheckbox, setCheckbox } from '../radio/RadioBlock';

/**
 * Представляет собой поле формы с чекбоксами
 */
export class RadioField extends Field {

   /**
    * Устанавливает значение поля
    *
    * @param value - id элементов блока
    */
   public setValue(value: string): void {
      this.resultInput.value = value;
      const selectedItemsIds: string[] = htmlArrDecode(value);

      const radioItems: HTMLElement[] = mQSA('[data-radio-item]', this.element);
      radioItems.forEach(item => {
         const itemId: string = safeDataAttrGetter('id', item);
         selectedItemsIds.includes(itemId) ? setCheckbox(item) : removeCheckbox(item);
      })
   }

}
