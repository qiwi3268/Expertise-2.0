import { MiscModal } from './MiscModal';
import { mQS } from '../../../lib/main';

/**
 * Представляет собой модальное окно одиночного справочника
 */
export class SingleMiscModal extends MiscModal {

   public constructor(select: HTMLElement) {
      super(select);
      this.resultInput = mQS('[data-misc-result]', this.field);

      this.handleClearButton();
   }

   /**
    * Обрабатывает нажатие на кнопку очисти выбранного значения справочника
    */
   protected handleClearButton(): void {
      const clearButton: HTMLElement | null = this.field.querySelector('[data-field-clear]');
      if (clearButton) {
         clearButton.addEventListener('click', () => this.removeSelectedValue());
      }
   }

   /**
    * Очищает выбранное значение справочника
    */
   public removeSelectedValue(): void {
      this.resultInput.value = '';
      this.title.textContent = 'Выберите значение';
   }

   /**
    * Выбирает элемент справочника
    *
    * @param event - событие клика по модальному окну
    */
   protected selectItem(event: MouseEvent): void {
      const deepestElem: Element = document.elementFromPoint(event.clientX, event.clientY)!;
      const miscItem: HTMLElement | null = deepestElem.closest('[data-misc-item]');
      if (miscItem) {
         this.setSelectedItem(miscItem);
         this.close();
      }
   }

}
