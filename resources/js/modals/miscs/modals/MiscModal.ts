import { mClosest, mQS, safeDataAttrGetter } from '../../../lib/main';
import { MiscModalManager } from '../MiscModalManager';
import { CacheSlots, PageManagers } from '../../../lib/Cache';
import { Cache } from '../../../lib/Cache';

/**
 * Описывает элемент справочника
 */
export type MiscItem = {
   /**
    * Идентификатор значения справочника
    */
   id: number | string,

   /**
    * Строковое значение справочника
    */
   label: string
}

/**
 * Представляет собой модальное окно справочника
 */
export abstract class MiscModal {

   /**
    * Блок, при нажатии на который открывается справочник
    */
   protected select: HTMLElement;

   /**
    * Поле, к которому относится справочник
    */
   protected field: HTMLElement;

   /**
    * Модальное окно справочника
    */
   protected modal: HTMLElement;

   /**
    * Блок, в котором отображается выбранный элемент справочника
    */
   protected title: HTMLElement;

   /**
    * Массив элементов справочника
    */
   protected items: HTMLElement[];

   /**
    * Блок, в котором расположены элементы справочника
    */
   protected container: HTMLElement;

   /**
    * Строки для поиска в справочнике
    */
   protected searchInput: HTMLInputElement;

   protected miscModalManager: MiscModalManager;

   /**
    * Скрытый инпут, в который записывается id выбранного элемента
    */
   protected resultInput: HTMLInputElement;

   /**
    * Создает объект справочника
    *
    * @param select - родительский селект
    */
   protected constructor(select: HTMLElement) {
      this.select = select;
      this.select.addEventListener('click', () => this.open());

      this.miscModalManager = Cache.slot(CacheSlots.PageManagers).get(PageManagers.MiscModal);

      this.field = mClosest('[data-field][data-type="misc"]', select);

      this.title = mQS('[data-field-label]', this.field);
      this.modal = mQS('[data-misc-modal]', this.field);

      this.handleItemsContainer();

      this.handleCloseButton();
      this.handleSearch();

   }

   /**
    * Обрабатывает нажатие внутри блока с элементами справочника
    */
   protected handleItemsContainer(): void {
      this.container = mQS('[data-misc-container]', this.modal);
      this.container.addEventListener('click', event => this.selectItem(event));
      this.items = Array.from(this.container.querySelectorAll('[data-misc-item]'));
   }

   protected abstract selectItem(event: MouseEvent): void;

   /**
    * Обрабатывает кнопку закрытия модального окна справочника
    */
   protected handleCloseButton(): void {
      const closeButton: HTMLElement = mQS('[data-misc-close]', this.modal);
      closeButton.addEventListener('click', () => this.close());
   }

   /**
    * Закрывает модальное окно справочника
    */
   public close(): void {
      this.modal.setAttribute('data-opened', 'false');
      this.miscModalManager.hideOverlay();
   }

   /**
    * Обрабатывает поиск по справочнику
    */
   private handleSearch(): void {
      let timerId: NodeJS.Timeout;
      this.searchInput = mQS('[data-misc-search]', this.modal);
      this.searchInput.addEventListener('input', () => {
         clearInterval(timerId);
         timerId = setInterval(() => {
            clearInterval(timerId);
            this.filterItemsContainer(this.searchInput.value.trim().toLowerCase());
         }, 500);
      });
   }

   /**
    * Оставляет в модальном окне только те значения, которые
    * содержат строку введенную в поле поиска по справочнику
    *
    * @param value - значение введенное в строку поиска
    */
   protected filterItemsContainer(value: string): void {
      this.container.textContent = '';
      const itemsToShow: HTMLElement[] = this.items.filter(item => item.innerHTML.toLowerCase().includes(value));
      itemsToShow.forEach(item => this.container.appendChild(item));
   }

   protected setSelectedItem(item: HTMLElement): void {
      this.resultInput.value = safeDataAttrGetter('id', item);
      this.title.textContent = item.innerHTML;
   }

   /**
    * Открывает модальное окно справочника
    */
   public open(): void {
      this.modal.setAttribute('data-opened', 'true');
      this.miscModalManager.setActiveMisc(this).showOverlay();
   }

}



