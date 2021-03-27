import { Calendar } from '../modals/Calendar';
import { mClosest, mQS } from '../lib/main';

/**
 * Представляет собой менеджер для работы с поля с датой
 */
export class CalendarManager {
   private static instance: CalendarManager;

   public static getInstance(): CalendarManager {

      if (!CalendarManager.instance) {
         CalendarManager.instance = new CalendarManager();
      }

      return CalendarManager.instance;
   }

   private constructor() {
   }

   /**
    * Инициализирует поля с датой на странице
    */
   public initPageDateFields(): void {
      const calendarSelects = document.querySelectorAll<HTMLElement>('[data-modal-select="calendar"]');
      calendarSelects.forEach(select => this.initDateField(select));
   }

   /**
    * Инициализирует поле с датой
    *
    * @param select - элемент, при клике на который вызывается календарь
    */
   private initDateField(select: HTMLElement): void {

      select.addEventListener('click', () => {
         Calendar.getInstance(select)
            .putFieldData(select)
            .setPosition()
            .open();
      });

      // вынести в отдельный модуль forms manager
      const parentField: HTMLElement = mClosest('[data-field]', select);
      const clearButton: HTMLElement | null = parentField.querySelector('[data-field-clear]');
      if (clearButton) {
         clearButton.addEventListener('click', () => {
            parentField.classList.remove('filled');
            const fieldLabel: HTMLElement = mQS('[data-field-label]', parentField);
            fieldLabel.textContent = 'Выберите дату';
            const resultInput: HTMLInputElement = mQS('[data-field-result]', parentField);
            resultInput.value = '';

         });
      }
   }

   /**
    * Инициализирует поля с датой в области действия
    *
    * @param scope - область действия
    */
   public initNewElementWithDateFields(scope: HTMLElement): void {
      scope.querySelectorAll<HTMLElement>('[data-modal-select="calendar"]')
         .forEach(scope => this.initDateField(scope));
   }
}
