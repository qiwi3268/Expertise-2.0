import { TypedPartForm } from './TypedPartForm';
import { ApplicationRadioBlocksManager } from '../../radio/ApplicationRadioBlocksManager';
import { CalendarManager } from '../../CalendarManager';
import { Cache, CacheSlots, PageManagers } from '../../../lib/Cache';
import { Dependencies, DependenciesHandler } from '../../../dependencies/DependenciesManager';
import { FileBlocksManager } from '../../files/FileBlocksManager';
import { MiscModalManager } from '../../../modals/miscs/MiscModalManager';
import { TemplatePart } from '../parts/TemplatePart';

/**
 * Представляет собой форму части шаблонного блока исполнителей из анкеты заявления
 */
export class ExecutorPartForm<T extends TemplatePart = TemplatePart> extends TypedPartForm<T> {

   /**
    * Инициализирует элементы формы
    */
   protected initFormElements(): void {
      super.initFormElements();

      ApplicationRadioBlocksManager.getInstance().handleNewRadioBlocksParentElement(this.partElement);

      Cache.slot(CacheSlots.PageManagers)
         .get<MiscModalManager>(PageManagers.MiscModal)
         .initNewElementWithMiscs(this.partElement);

      CalendarManager.getInstance().initNewElementWithDateFields(this.partElement);
      FileBlocksManager.getInstance().initNewElementWithFileBlocks(this.partElement);
   }

   /**
    * Получает менеджеры зависимостей, относящиеся к форме
    */
   public getDependenciesManagers(): DependenciesHandler[] {

      return [
         Cache.slot(CacheSlots.FieldDependencies).get(Dependencies.SingledDisplay),
         Cache.slot(CacheSlots.FieldDependencies).get(Dependencies.MultipleDisplay),
      ];
   }
}
