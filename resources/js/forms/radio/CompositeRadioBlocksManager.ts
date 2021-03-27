import { RadioBlocksManager } from './RadioBlocksManager';
import { isDisplayedElement } from '../../lib/main';

/**
 * Представляет собой менеджер для работы со сложными чекбокс блоками
 */
export abstract class CompositeRadioBlocksManager extends RadioBlocksManager {

   /**
    * Инициализирует блоки с чекбоксами на странице
    */
   public initPageRadioBlocks(): void {
      const nonTemplateRadioBlockElements: HTMLElement[] = this.radioBlockElements.filter(isDisplayedElement);
      nonTemplateRadioBlockElements.forEach(block => this.initRadioBlock(block));
   }

   /**
    * Инициализирует блок с чекбоксами
    *
    * @param block - элемент блока
    */
   public abstract initRadioBlock(block: HTMLElement): void;

   /**
    * Инициализирует блоки с чекбоксами в области действия
    *
    * @param scope - область действия
    */
   public handleNewRadioBlocksParentElement(scope: HTMLElement): void {
      scope.querySelectorAll<HTMLElement>('[data-field][data-type="radio"]')
         .forEach(radioBlock => this.initRadioBlock(radioBlock));
   }

}
