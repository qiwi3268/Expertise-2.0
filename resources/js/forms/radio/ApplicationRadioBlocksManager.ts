import { RadioBlock } from './RadioBlock';
import { CompositeRadioBlocksManager } from './CompositeRadioBlocksManager';
import { DependentRadioBlock } from './DependentRadioBlock';

/**
 * Представляет собой менеджер для работы с чекбокс блоками анкеты заявления
 */
export class ApplicationRadioBlocksManager extends CompositeRadioBlocksManager {

   private static instance: ApplicationRadioBlocksManager;

   private constructor() {
      super();
   }

   public static getInstance(): ApplicationRadioBlocksManager {
      if (!ApplicationRadioBlocksManager.instance) {
         ApplicationRadioBlocksManager.instance = new ApplicationRadioBlocksManager();
      }

      return ApplicationRadioBlocksManager.instance;
   }

   /**
    * Инициализирует блок с чекбоксами
    *
    * @param block - элемент блока
    */
   public initRadioBlock(block: HTMLElement): void {
      block.hasAttribute('data-sub-radio') ? new DependentRadioBlock(block) : new RadioBlock(block);
   }


}
