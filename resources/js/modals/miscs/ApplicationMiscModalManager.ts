import { MiscModalManager } from './MiscModalManager';
import { MiscModal } from './modals/MiscModal';
import { isNotTemplate, mClosest } from '../../lib/main';
import { LogicError } from '../../lib/LogicError';
import { ExecutorEqualityMiscModal } from './modals/ExecutorEqualityMiscModal';
import { MultipleMiscModal } from './modals/MultipleMiscModal';
import { SingleMiscModal } from './modals/SingleMiscModal';
import { DependentMiscModal } from './modals/DependentMiscModal';
import { Cache, CacheSlots, PageManagers } from '../../lib/Cache';
import { FormMultipleDependentMiscModal } from './modals/FormMultipleDependentMiscModal';

/**
 * Типы справочников в анкете заявления
 */
export enum MiscModalType {
   FormMisc = 'formMisc',
   FormDependentMisc = 'formDependentMisc',
   ExecutorEqualityMisc = 'executorEqualityMisc'
}

/**
 * Представляет собой менеджер для работы со справочниками анкеты заявления
 */
export class ApplicationMiscModalManager extends MiscModalManager {

   private static instance: ApplicationMiscModalManager;

   public static create(): ApplicationMiscModalManager {

      if (ApplicationMiscModalManager.instance) {
         new LogicError('ApplicationMiscModalManager уже создан');
      } else {
         ApplicationMiscModalManager.instance = new ApplicationMiscModalManager();
      }

      return ApplicationMiscModalManager.instance;
   }

   private constructor() {
      super();
      Cache.slot(CacheSlots.PageManagers).set(PageManagers.MiscModal, this);
   }

   /**
    * Инициализирует справочники на странице
    */
   public initPageMiscModals(): void {
      const miscSelects: HTMLElement[] = Array.from(document.querySelectorAll('[data-misc-select]'));
      miscSelects
         .filter(select => isNotTemplate(select))
         .forEach(select => this.initMiscSelect(select));
   }

   /**
    * Создает объект модального окна справочника
    *
    * @param select - элемент, при клике на который вызывается справочник
    */
   protected createMiscModal(select: HTMLElement): MiscModal {
      let miscModal: MiscModal | null = null;
      const parentField: HTMLElement = mClosest('[data-field][data-type="misc"]', select);
      const multiple: boolean = parentField.dataset.multiple === 'true';

      switch (parentField.dataset.miscType) {
         case MiscModalType.FormMisc:
            miscModal = multiple ? new MultipleMiscModal(select) : new SingleMiscModal(select);
            break;
         case MiscModalType.FormDependentMisc:
            miscModal = multiple ? new FormMultipleDependentMiscModal(select) : new DependentMiscModal(select);
            break;
         case MiscModalType.ExecutorEqualityMisc:
            miscModal = new ExecutorEqualityMiscModal(select);
            break;
         default:
            new LogicError(`Не удалось определить тип справочника при создании`);
      }

      return miscModal!;
   }


}
