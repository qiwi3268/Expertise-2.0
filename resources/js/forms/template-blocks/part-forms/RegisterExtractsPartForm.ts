import { PartForm } from './PartForm';
import { MultipleTemplatePart } from '../parts/MultipleTemplatePart';
import { CalendarManager } from '../../CalendarManager';
import { ApplicationRadioBlocksManager } from '../../radio/ApplicationRadioBlocksManager';
import { Dependencies, DependenciesHandler } from '../../../dependencies/DependenciesManager';
import { Cache, CacheSlots } from '../../../lib/Cache';
import { FileBlocksManager } from '../../files/FileBlocksManager';
import { addFieldMutationHandler } from '../../../lib/main';

/**
 * Представляет собой форму части шаблонного блока выписок СРО
 */
export class RegisterExtractsPartForm extends PartForm<MultipleTemplatePart> {

   /**
    * Инициализирует элементы формы
    */
   protected initFormElements(): void {
      super.initFormElements();

      ApplicationRadioBlocksManager.getInstance().handleNewRadioBlocksParentElement(this.partElement);
      CalendarManager.getInstance().initNewElementWithDateFields(this.partElement);
      FileBlocksManager.getInstance().initNewElementWithFileBlocks(this.partElement);

      this.getDependenciesManager().handleNewMainFieldsParentElement(this.partElement);

      this.handleMainFieldMutation();
   }

   /**
    * Обрабатывает изменения главного поля
    */
   protected handleMainFieldMutation(): void {
      addFieldMutationHandler(this.mainField.getElement(), (fieldValue: string) => {
         this.parentPart.setPartLabel(fieldValue);
      });
   }

   /**
    * Получает менеджеры зависимостей, относящиеся к форме
    */
   private getDependenciesManager(): DependenciesHandler {
      return Cache.slot(CacheSlots.FieldDependencies).get(Dependencies.SingledDisplay)
   }

}
