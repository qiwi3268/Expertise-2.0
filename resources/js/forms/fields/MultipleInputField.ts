import { InputField } from './InputField';
import { mQS } from '../../lib/main';

export class MultipleInputField extends InputField {

   protected parts: Set<HTMLElement>;
   protected createPart: Function;

   public constructor(element: HTMLElement, createPart: Function) {
      super(element);

      // this.parts = [mQS('[data-field-part]', this.element)];
      this.parts = new Set<HTMLElement>();
      this.parts.add(mQS('[data-field-part]', this.element));

      this.createPart = createPart;

   }

   protected initValidation(): void {

   }

   protected handleAddPartButton(): void {
      const addPartButton: HTMLElement = mQS('data-field-add', this.element);
      addPartButton.addEventListener('click', () => {
         const newPart: HTMLElement = this.createPart();
         this.parts.add(newPart);

         const removePartButton: HTMLElement = mQS('data-field-remove-part', newPart);
         removePartButton.addEventListener('click', () => {
            this.parts.delete(newPart);
            newPart.remove();
         })

      });
   }

   public createInputPart(): HTMLElement {
      const fieldRow: HTMLElement = document.createElement('DIV');
      fieldRow.classList.add('form-field__row');

      const rowInput: HTMLElement = document.createElement('INPUT');
      rowInput.classList.add('form-field__input');
      rowInput.setAttribute('data-field-result', '');
      rowInput.setAttribute('placeholder', 'Введите значение');
      rowInput.setAttribute('value', '');

      const removeRowButton: HTMLElement = document.createElement('I');
      removeRowButton.classList.add('form-field__icon-remove', 'fas', 'fa-minus');
      removeRowButton.setAttribute('data-field-remove-part', '');

      fieldRow.appendChild(rowInput);
      fieldRow.appendChild(removeRowButton);

      return fieldRow;
   }

}
