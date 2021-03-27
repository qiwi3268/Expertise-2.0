import { TypedPartForm } from './TypedPartForm';
import { ApplicationRadioBlocksManager } from '../../radio/ApplicationRadioBlocksManager';
import { MultipleTemplatePart } from '../parts/MultipleTemplatePart';
import { addFieldMutationHandler, mQS } from '../../../lib/main';
import { Cache, CacheSlots, PageManagers } from '../../../lib/Cache';
import { MiscModalManager } from '../../../modals/miscs/MiscModalManager';
import { Dependencies, DependenciesHandler } from '../../../dependencies/DependenciesManager';

/**
 * Представляет собой форму части шаблонного блока источников финансирования
 */
export class FinancingSourcePartForm extends TypedPartForm {

   /**
    * Родительская части
    */
   protected parentPart: MultipleTemplatePart;

   /**
    * Инициализирует элементы формы
    */
   protected initFormElements(): void {
      super.initFormElements();

      ApplicationRadioBlocksManager.getInstance().handleNewRadioBlocksParentElement(this.partElement);

      Cache.slot(CacheSlots.PageManagers)
         .get<MiscModalManager>(PageManagers.MiscModal)
         .initNewElementWithMiscs(this.partElement);

      this.handleMainFieldMutation();
   }

   /**
    * Обрабатывает изменения главного поля
    */
   protected handleMainFieldMutation(): void {
      addFieldMutationHandler(this.mainField.getElement(), (fieldValue: string) => {
         const selectedItem: HTMLElement | null = this.mainField.getElement().querySelector(
            `[data-radio-item][data-id="${fieldValue}"]`
         );

         if (selectedItem !== null) {
            const itemLabel: HTMLElement = mQS('[data-radio-text]', selectedItem);
            this.parentPart.setPartLabel(itemLabel.innerHTML);
         } else {
            this.parentPart.setPartLabel('...');
         }


      });
   }

   /**
    * Получает менеджеры зависимостей, относящиеся к форме
    */
   public getDependenciesManagers(): DependenciesHandler[] {

      return [
         Cache.slot(CacheSlots.FieldDependencies).get(Dependencies.SingledDisplay),
      ];
   }

}
