import { isDisplayedElement } from '../../lib/main';
import { TemplateBlock } from './blocks/TemplateBlock';

/**
 * Представляет собой менеджер шаблонных блоков
 */
export abstract class TemplateBlocksManager {

   /**
    * Создает шаблонный блок
    *
    * @param element - элемент шаблонного блока
    */
   protected abstract createTemplateBlock(element: HTMLElement): TemplateBlock;

   /**
    * Инициализирует шаблонные блоки на странице
    */
   public initPageTemplateBlocks(): void {
      this.initNewTemplateBlocks(document.documentElement);
   }

   /**
    * Инициализирует шаблонные блоки в области действия
    *
    * @param scope - область действия
    */
   public initNewTemplateBlocks(scope: HTMLElement): void {
      const templateBlocks: NodeListOf<HTMLElement> = scope.querySelectorAll('[data-template-block]');
      Array.from(templateBlocks)
         .filter(isDisplayedElement)
         .forEach(templateBlock => this.createTemplateBlock(templateBlock));
   }


}
