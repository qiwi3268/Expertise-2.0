import { SingleMiscModal } from './SingleMiscModal';
import { MiscItem } from './MiscModal';
import { safeDataAttrGetter, safeMapGetter } from '../../../lib/main';
import { ErrorModal } from '../../ErrorModal';
import { MiscDependenciesManager } from '../MiscDependenciesManager';

/**
 * Представляет собой зависимый справочник
 */
export interface SubMisc {

   /**
    * Удаляет текущие элементы
    */
   removeItems(): void;

   /**
    * Получает элементы в зависимости от значения родительского поля
    */
   getItems(): void;

   /**
    * Получает зависимого справочника
    */
   getName(): string;
}

/**
 * Представляет собой модальное окно зависимого справочника
 */
export class DependentMiscModal extends SingleMiscModal implements SubMisc {

   /**
    * Наименование справочника
    */
   private readonly name: string;

   /**
    * Сообщение с ошибкой при получении значений
    */
   private readonly errorMessage: string;

   /**
    * Менеджер зависимостей справочников
    */
   private miscDependenciesManager: MiscDependenciesManager;

   public constructor(select: HTMLElement) {
      super(select);
      this.name = safeDataAttrGetter('name', this.field);
      this.errorMessage = safeDataAttrGetter('errorMessage', this.field);

      this.miscDependenciesManager = MiscDependenciesManager.getInstance();
      this.miscDependenciesManager.addSubMiscValue(this);
   }

   /**
    * Открывает модальное окно справочника
    */
   public open(): void {

      this.miscDependenciesManager.getItemsBySubMiscName(this.name)
         .then((items: MiscItem[]) => {
            this.container.textContent = '';
            items.forEach(item => this.items.push(this.createMiscItem(item)));
            super.open();
         })
         .catch(() => {
            ErrorModal.open(
               'Ошибка при открытии справочника',
               this.errorMessage
            );
            this.close();
         });

   }

   public getItems(): void {
   }

   /**
    * Создает элемент справочника и добавляет его в модальное окно
    *
    * @param itemData - данные элемента справочника
    */
   protected createMiscItem(itemData: MiscItem): HTMLElement {

      const itemElem: HTMLElement = document.createElement('DIV');
      itemElem.classList.add('misc__item');
      itemElem.setAttribute('data-misc-item', '');
      itemElem.setAttribute('data-id', itemData.id.toString());
      itemElem.textContent = itemData.label;
      this.container.appendChild(itemElem);

      return itemElem;
   }

   /**
    * Удаляет элементы справочника
    */
   public removeItems(): void {
      this.container.textContent = '';
      this.items = [];
      this.removeSelectedValue();
   }

   /**
    * Получает наименование справочника
    */
   public getName(): string {
      return this.name;
   }

}
