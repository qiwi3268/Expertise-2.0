import { safeDataAttrGetter } from '../../../lib/main';
import { MiscDependenciesManager } from '../MiscDependenciesManager';
import { SubMisc } from './DependentMiscModal';
import { MultipleMiscModal } from './MultipleMiscModal';
import { MiscItem } from './MiscModal';
import { ErrorModal } from '../../ErrorModal';

/**
 * Представляет собой модальное окно множественного зависимого справочника
 */
export class FormMultipleDependentMiscModal extends MultipleMiscModal implements SubMisc {

   /**
    * Наименование справочника
    */
   private readonly name: string;

   /**
    * Получает зависимого справочника
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
            //не удалять и не брать новые если открывается одно и то же
            this.container.textContent = '';
            this.items = [];
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
    * Создает элемент справочника
    *
    * @param itemData - данные об элементе
    */
   protected createMiscItem(itemData: MiscItem): HTMLElement {

      const itemElem: HTMLElement = document.createElement('DIV');
      itemElem.classList.add('misc__item');
      itemElem.setAttribute('data-misc-item', '');
      itemElem.setAttribute('data-id', itemData.id.toString());

      const itemSwitch: HTMLElement = document.createElement('I');
      itemSwitch.classList.add('misc__switch', 'fas', 'fa-toggle-off');
      itemSwitch.setAttribute('data-switch-icon', '');

      const itemLabel: HTMLElement = document.createElement('DIV');
      itemLabel.classList.add('misc__label');
      itemLabel.setAttribute('data-misc-item-label', '');
      itemLabel.textContent = itemData.label;

      itemElem.appendChild(itemSwitch);
      itemElem.appendChild(itemLabel);
      this.container.appendChild(itemElem);

      return itemElem;
   }

   /**
    * Удаляет элементы справочника из модального окна
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
