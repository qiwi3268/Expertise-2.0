import { RadioBlock } from './RadioBlock';
import { SubMisc } from '../../modals/miscs/modals/DependentMiscModal';
import { MiscItem } from '../../modals/miscs/modals/MiscModal';
import { safeDataAttrGetter } from '../../lib/main';
import { MiscDependenciesManager } from '../../modals/miscs/MiscDependenciesManager';

/**
 * Представляет собой зависимый блок с чекбоксами
 */
export class DependentRadioBlock extends RadioBlock implements SubMisc {

   /**
    * Наименование поля
    */
   private readonly name: string;

   /**
    * Сообщение во время получения значений
    */
   private readonly uploadingMessage: string;

   /**
    * Сообщение с ошибкой при получении значений
    */
   private readonly errorMessage: string;

   /**
    * Менеджер зависимостей справочников
    */
   private readonly miscDependenciesManager: MiscDependenciesManager;

   /**
    * Текущие значения
    */
   private items: MiscItem[] = [];

   public constructor(element: HTMLElement) {
      super(element);

      this.name = safeDataAttrGetter('name', this.field);
      this.uploadingMessage = safeDataAttrGetter('uploadingMessage', this.field);
      this.errorMessage = safeDataAttrGetter('errorMessage', this.field);

      this.miscDependenciesManager = MiscDependenciesManager.getInstance();
      this.miscDependenciesManager.addSubMiscValue(this);
   }

   /**
    * Удаляет элементы справочника
    */
   public removeItems(): void {
      this.container.textContent = this.uploadingMessage;
      this.resultInput.value = '';
   }

   /**
    * Получает значения справочника в зависимости от значения главного поля
    */
   public getItems(): void {

      this.miscDependenciesManager.getItemsBySubMiscName(this.name)
         .then((items: MiscItem[]) => {

            if (items.length > 0) {
               this.container.textContent = '';
               this.items = items;
               this.items.forEach((item: MiscItem) => this.appendNewItem(item));
            } else {
               this.container.textContent = this.errorMessage;
            }

         })
         .catch(() => {
            this.container.textContent = this.errorMessage;
         });
   }

   /**
    * Добавляет чекбокс
    *
    * @param item - элемент чекбокса
    */
   private appendNewItem(item: MiscItem): void {
      const element: HTMLElement = this.createItemElement(item);
      this.initItem(element);
      this.container.appendChild(element);
   }

   /**
    * Создает элемент чекбокса
    *
    * @param item - данные об элементе
    */
   private createItemElement(item: MiscItem): HTMLElement {
      const radioElement: HTMLElement = document.createElement('DIV');
      radioElement.classList.add('radio__item');
      radioElement.setAttribute('data-radio-item', '');
      radioElement.setAttribute('data-id', item.id.toString());

      const icon: HTMLElement = document.createElement('I');
      icon.classList.add('radio__icon', 'far', 'fa-square');
      icon.setAttribute('data-radio-icon', '');
      radioElement.appendChild(icon);

      const label: HTMLElement = document.createElement('SPAN');
      label.classList.add('radio__text');
      label.setAttribute('data-radio-text', '');
      label.textContent = item.label;
      radioElement.appendChild(label);

      return radioElement;
   }

   /**
    * Получает наименование справочника
    */
   public getName(): string {
      return this.name;
   }
}
