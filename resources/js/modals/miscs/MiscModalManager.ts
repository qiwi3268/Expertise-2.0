import { MiscModal } from './modals/MiscModal';
import { mGetByID } from '../../lib/main';

/**
 * Представляет собой менеджер для работы со справочниками
 */
export abstract class MiscModalManager {

   /**
    * Фон модальных окон справочников
    */
   protected overlay: HTMLElement;

   /**
    * Открытый в данный момент справочник
    */
   protected activeMisc: MiscModal;


   protected constructor() {
      this.overlay = mGetByID('miscOverlay');
      this.overlay.addEventListener('click', () => this.activeMisc.close());
   }

   /**
    * Инициализирует справочники на странице
    */
   public initPageMiscModals(): void {
      const miscSelects: NodeListOf<HTMLElement> = document.querySelectorAll('[data-misc-select]');
      miscSelects.forEach(select => this.initMiscSelect(select));
   }

   /**
    * Инициализирует поле справочника
    *
    * @param select - элемент, при клике на который вызывается справочник
    */
   protected initMiscSelect(select: HTMLElement): void {
      select.addEventListener(
         'click',
         () => this.createMiscModal(select).open(),
         {once: true}
      );
   }

   /**
    * Создает объект модального окна справочника
    *
    * @param select - элемент, при клике на который вызывается справочник
    */
   protected abstract createMiscModal(select: HTMLElement): MiscModal;

   /**
    * Устанавливает активный справочник
    *
    * @param misc - справочник открытый в данный момент
    */
   public setActiveMisc(misc: MiscModal): MiscModalManager {
      this.activeMisc = misc;
      return this;
   }

   /**
    * Скрывает фон модального окна
    */
   public hideOverlay(): void {
      this.overlay.setAttribute('data-opened', 'false');
   }

   /**
    * Отображает фон модального окна
    */
   public showOverlay(): void {
      this.overlay.setAttribute('data-opened', 'true');
   }

   /**
    * Инициализирует справочники в области действия
    *
    * @param scope - область действия
    */
   public initNewElementWithMiscs(scope: HTMLElement): void {
      scope.querySelectorAll<HTMLElement>('[data-misc-select]')
         .forEach(select => this.initMiscSelect(select));
   }


}
