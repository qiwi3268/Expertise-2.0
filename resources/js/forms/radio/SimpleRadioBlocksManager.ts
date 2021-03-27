import { RadioBlock } from './RadioBlock';
import { RadioBlocksManager } from './RadioBlocksManager';

/**
 * Представляет собой менеджер для работы с простыми чекбокс блоками
 */
export class SimpleRadioBlocksManager extends RadioBlocksManager {

   private static instance: SimpleRadioBlocksManager;

   protected constructor() {
      super();
   }

   public static getInstance(): SimpleRadioBlocksManager {
      if (!SimpleRadioBlocksManager.instance) {
         SimpleRadioBlocksManager.instance = new SimpleRadioBlocksManager();
      }

      return SimpleRadioBlocksManager.instance;
   }

   /**
    * Инициализирует блоки с чекбоксами на странице
    */
   public initPageRadioBlocks(): void {
      this.radioBlockElements.forEach(block => this.initRadioBlock(block));
   }

   /**
    * Инициализирует блок с чекбоксами
    *
    * @param block - элемент блока
    */
   public initRadioBlock(block: HTMLElement): void {
      new RadioBlock(block);
   }

}
