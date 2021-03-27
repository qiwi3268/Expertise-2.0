/**
 * Представляет собой менеджер для работы с чекбокс блоками
 */
export abstract class RadioBlocksManager {

   /**
    * Блоки с чекбоксами
    */
   protected radioBlockElements: HTMLElement[];

   protected constructor() {
      this.radioBlockElements = Array.from(document.querySelectorAll('[data-field][data-type="radio"]'));
   }

   /**
    * Инициализирует блоки с чекбоксами на странице
    */
   public abstract initPageRadioBlocks(): void;

   /**
    * Инициализирует блок с чекбоксами
    *
    * @param block - элемент блока
    */
   public abstract initRadioBlock(block: HTMLElement): void;

}
