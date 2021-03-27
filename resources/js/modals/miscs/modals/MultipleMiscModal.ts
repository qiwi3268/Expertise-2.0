import { SingleMiscModal } from './SingleMiscModal';
import { offSwitch, toggleSwitch } from '../../../forms/radio/RadioBlock';
import { htmlArrEncode, mQS, safeDataAttrGetter } from '../../../lib/main';

/**
 * Представляет собой модальное окно множественного справочника
 */
export class MultipleMiscModal extends SingleMiscModal {

   public constructor(select: HTMLElement) {
      super(select);

      this.handleActions();
   }

   /**
    * Обрабатывает действия в модальном окне
    */
   protected handleActions(): void {
      const unselectButton: HTMLElement = mQS('[data-misc-unselect]', this.modal);
      unselectButton.addEventListener('click', () => this.unselectAll());

      const selectButton: HTMLElement = mQS('[data-misc-submit]', this.modal);
      selectButton.addEventListener('click', () => {
         const selectedItems: HTMLElement[] = this.items.filter(item => item.dataset.selected === 'true')
         selectedItems.length > 0 ? this.setSelectedItems(selectedItems) : this.removeSelectedValue();
         this.close();
      });
   }

   /**
    * Снимает выбор всех элементов
    */
   protected unselectAll(): void {
      this.items.forEach(item => offSwitch(item));
   }

   /**
    * Отмечает элемент справочника, как выбранный
    *
    * @param event - событие клика по модальному окну
    */
   protected selectItem(event: MouseEvent): void {
      const deepestElem: Element = document.elementFromPoint(event.clientX, event.clientY)!;
      const miscItem: HTMLElement | null = deepestElem.closest('[data-misc-item]');
      if (miscItem) {
         toggleSwitch(miscItem);
      }
   }

   /**
    * Записывает в результат выбранные элементы справочника
    *
    * @param selectedItems - выбранные элементы
    */
   protected setSelectedItems(selectedItems: HTMLElement[]): void {
      const selectedIds: string[] = selectedItems.map(item => safeDataAttrGetter('id', item));
      this.resultInput.value = htmlArrEncode(selectedIds);

      this.title.textContent = '';
      selectedItems.forEach(item => {
         const label: HTMLElement = mQS('[data-misc-item-label]', item);
         this.appendLabel(label.innerHTML)
      });

   }

   /**
    * Очищает выбранное значение справочника
    */
   public removeSelectedValue(): void {
      this.resultInput.value = '';
      this.unselectAll();
      this.title.textContent = '';
      this.appendLabel('Выберите одно или несколько значений');
   }

   /**
    * Добавляет в родительское поле наименование выбранного элемента
    *
    * @param text - наименование элемента справочника
    */
   protected appendLabel(text: string): void {
      const label: HTMLElement = document.createElement('DIV');
      label.classList.add('form-field__label');
      label.textContent = text;
      this.title.appendChild(label);
   }

   /**
    * Оставляет в модальном окне только те значения, которые
    * содержат строку введенную в поле поиска по справочнику
    *
    * @param value - значение введенное в строку поиска
    */
   protected filterItemsContainer(value: string): void {
      this.container.textContent = '';
      const itemsToShow: HTMLElement[] = this.items.filter(item => {
         const itemLabel: HTMLElement = mQS('[data-misc-item-label]', item);
         return itemLabel.innerHTML.toLowerCase().includes(value);
      });
      itemsToShow.forEach(item => this.container.appendChild(item));
   }



}


