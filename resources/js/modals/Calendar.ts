import {
   Coordinates,
   getDateFromString,
   getElementCoords,
   mClosest,
   mGetByID,
   mQS,
   safeDataAttrGetter
} from '../lib/main';
import { ErrorModal } from './ErrorModal';
import { LogicError } from '../lib/LogicError';

/**
 * Доступный интервал дат для выбора
 * <br> 1 - сегодня или позже
 * <br> 0 - любая дата
 * <br> -1 - сегодня или раньше
 */
enum DateInterval {
   Future = '1',
   Any = '0',
   Past = '-1'
}

/**
 * Класс представляет собой модальное окно календаря
 */
export class Calendar {

   /**
    * Инстанс модального окна календаря
    */
   private static instance: Calendar;

   /**
    * Фон модального окна
    */
   private overlay: HTMLElement;

   /**
    * Поле для выбора даты
    */
   private select: HTMLElement;

   /**
    * Родительское поле
    */
   private field: HTMLElement;

   /**
    * Блок, в который подставляется выбранная дата
    */
   private fieldLabel: HTMLElement;

   /**
    * Доступный интервал дат для выбора
    */
   private interval: DateInterval;

   /**
    * Элемент модального окна
    */
   private readonly modal: HTMLElement;

   /**
    * Блок, в который помещается содержимое календаря
    */
   private body: HTMLElement;

   /**
    * Блок с днями недели
    */
   private title: HTMLElement;

   /**
    * Дата в заголовке
    */
   private selectedDateLabel: HTMLElement;

   /**
    * Выбранный день
    */
   private selectedDay: number;

   /**
    * Выбранный месяц
    */
   private selectedMonth: number;

   /**
    * Выбранный год
    *
    * @type {number}
    */
   private selectedYear: number;

   /**
    * Отображаемая дата
    */
   private currentDate: Date;

   /**
    * Отображаемый месяц
    */
   private currentMonth: number;

   /**
    * Отображаемый год
    */
   private currentYear: number;

   /**
    * Строка с элементами календаря
    */
   private currentRow: HTMLElement;

   /**
    * Отображаемый уровень календаря
    * 3 - дни
    * 2 - месяцы
    * 1 - годы
    */
   private level: 1 | 2 | 3;

   /**
    * Скрытый инпут поля с датой, в который записывается выбранная дата
    */
   private resultInput: HTMLInputElement;

   /**
    * Получает объект модального поля календаря
    *
    * @param select - файловое поле
    * @return инстанс календаря
    */
   public static getInstance(select: HTMLElement): Calendar {

      if (!this.instance) {
         this.instance = new Calendar(select);
      }

      return this.instance;
   }

   /**
    * Создает объект модального окна календаря
    */
   private constructor(select: HTMLElement) {
      this.select = select;

      this.modal = mGetByID('calendar');
      this.body = mGetByID('calendarBody');
      this.title = mGetByID('calendarTitle');
      this.selectedDateLabel = mGetByID('calendarLabel');

      this.currentDate = new Date();

      // this.init();
      this.handleDateLabel();
      this.handleArrows();
      this.handleOverlay();
   }

   /**
    * Инициализирует календарь
    */
   private init(): void {
      this.selectedDay = this.currentDate.getDate();
      this.selectedMonth = this.currentDate.getMonth();
      this.currentMonth = this.currentDate.getMonth();
      this.selectedYear = this.currentDate.getFullYear();
      this.currentYear = this.currentDate.getFullYear();

      this.level = 3;
      this.putItems();
   }

   /**
    * Добавляет элементы в календарь в зависимости от выбранного уровня
    */
   private putItems(): void {
      this.body.textContent = '';
      this.createRow();

      // В зависимости от уровня добавляем элементы в календарь, строку с днями недели и
      // отображаем текущую дату в заголовке
      switch (this.level) {
         case 3 :
            this.putDays();
            this.title.classList.remove('hidden');
            this.selectedDateLabel.textContent = `${this.getFullMonthString()} ${this.currentYear}`;
            break;
         case 2 :
            this.putMonths();
            this.title.classList.add('hidden');
            this.selectedDateLabel.textContent = this.currentYear.toString();
            break;
         case 1 :
            this.putYears();
            this.title.classList.add('hidden');
            this.selectedDateLabel.textContent = `${this.currentYear - 5} - ${this.currentYear + 6}`
      }
   }

   /**
    * Создает строку для элементов календаря
    */
   private createRow(): void {
      this.currentRow = document.createElement('DIV');
      this.currentRow.classList.add('calendar__row');
      this.body.appendChild(this.currentRow);
   }

   /**
    * Добавляет дни в календарь
    */
   private putDays(): void {
      const currentDate: Date = new Date(this.currentYear, this.currentMonth);

      // добавляем в начало календаря дни из пердыдущего месяца и берем их количество
      const previousMonthDaysCount: number = this.putPreviousMonthDaysAndGetCount(currentDate);

      for (let i = 0; i < 42 - previousMonthDaysCount; i++) {
         this.putDay(currentDate.getDate(), currentDate.getMonth(), currentDate.getFullYear());
         currentDate.setDate(currentDate.getDate() + 1);
      }
   }

   /**
    * Добавляет в текущий месяц в каледаре дни из прошлого месяца
    *
    * @param currentDate - текущая дата
    * @return количество добавленных дней из прошлого месяца
    */
   private putPreviousMonthDaysAndGetCount(currentDate: Date): number {
      let daysCounter = 0;

      // Номер дня недели первого дня текущего месяца
      const firstWeekDay: number = currentDate.getDay();

      // От первого дня текущего месяца отнимаем количество дней
      // в первой неделе из прошлого месяца
      const previousMonthLastDay: number = currentDate.getDate() - firstWeekDay + 1;

      // День из прошлого месяца, с которого начинается первая неделя
      const previousMonth: Date = new Date(currentDate.getFullYear(), currentDate.getMonth(), previousMonthLastDay);

      for (let j = firstWeekDay - 1; j > 0; j--) {
         this.putDay(previousMonth.getDate(), previousMonth.getMonth(), previousMonth.getFullYear());
         previousMonth.setDate(previousMonth.getDate() + 1);
         daysCounter++;
      }

      return daysCounter;
   }

   /**
    * Добавляет элемент дня в календарь
    *
    * @param currentDay - отображаемый день
    * @param currentMonth - отображаемый месяц
    * @param currentYear - отображаемый год
    */
   private putDay(currentDay: number, currentMonth: number, currentYear: number): void {
      const dayElement: HTMLElement = this.createDay(currentDay, currentMonth, currentYear);
      dayElement.addEventListener('click', () => this.selectDay(dayElement));
   }

   /**
    * Устанавливает выбранный день после проверки
    *
    * @param dayElement - элемент дня
    */
   private selectDay(dayElement: HTMLElement): void {

      const newYear: number = parseInt(safeDataAttrGetter('year', dayElement));
      if (!this.isValidYear(newYear)) {
         ErrorModal.open('Ошибка при выборе даты', 'Для выбора даты доступен год больше 1500 и меньше 2200');
         return;
      }

      const newMonth: number = parseInt(safeDataAttrGetter('month', dayElement));
      const newDay: number = parseInt(dayElement.innerHTML);
      const selectedDate: Date = new Date(newYear, newMonth, newDay);

      if (this.interval === DateInterval.Future && !this.isFutureDate(selectedDate)) {
         ErrorModal.open('Ошибка при выборе даты', 'Для данного поля недоступна прошедшая дата');
      } else if (this.interval === DateInterval.Past && !this.isPastDate(selectedDate)) {
         ErrorModal.open('Ошибка при выборе даты', 'Для данного поля недоступна будущая дата');
      } else {

         this.currentDate.setFullYear(newYear);

         if (dayElement.classList.contains('previous') || dayElement.classList.contains('next')) {
            this.changeCurrentMonth(newMonth);
         }

         this.setSelectedDate(selectedDate);
         this.removeSelectedItem();
         this.changeSelectedDay();
         this.close();
      }

   }

   /**
    * Определяет, является ли дата сегодняшней или будущей
    * @param date - проверяемая дата
    * @returns {boolean}
    */
   private isFutureDate(date: Date): boolean {
      const now = new Date();
      now.setHours(0, 0, 0, 0);

      return date.getTime() >= now.getTime();
   }

   /**
    * Определяет, является ли дата сегодняшней или прошедшей
    * @param date - проверяемая дата
    * @returns {boolean}
    */
   private isPastDate(date: Date): boolean {
      const now = new Date();
      now.setHours(0, 0, 0, 0);

      return date.getTime() <= now.getTime();
   }

   /**
    * Создает элемент дня
    *
    * @param currentDay - отображаемый день
    * @param currentMonth - отображаемый месяц
    * @param currentYear - отображаемый год
    * @return элемент дня для календаря
    */
   private createDay(currentDay: number, currentMonth: number, currentYear: number): HTMLElement {
      const dayElement: HTMLElement = this.createItem('day', 7);

      // записывает месяц и год, к которому относится день
      dayElement.dataset.year = currentYear.toString();
      dayElement.dataset.month = currentMonth.toString();

      // если день из прошлого или следующего месяца
      if (currentMonth > this.currentMonth) {
         dayElement.classList.add('next');
      } else if (currentMonth < this.currentMonth) {
         dayElement.classList.add('previous');
      }

      dayElement.textContent = currentDay.toString();

      // Если день сопадает с выбранной датой, отображаем его как выбранный
      if (parseInt(dayElement.innerHTML) === this.selectedDay &&
         parseInt(dayElement.dataset.month) === this.selectedMonth &&
         parseInt(dayElement.dataset.year) === this.selectedYear
      ) {
         dayElement.classList.add('selected');
      }

      return dayElement;
   }

   /**
    * Создает и добавляет элемент календаря в строку
    *
    * @param type - тип элемента (день, месяц, год)
    * @param rowSize - размер строки с элементами
    * @return элемент календаря
    */
   private createItem(type: 'day' | 'month' | 'year', rowSize: number): HTMLElement {
      if (this.currentRow.childElementCount === rowSize) {
         this.createRow();
      }

      const item: HTMLElement = document.createElement('DIV');
      item.classList.add('calendar__item');
      item.dataset.type = type;

      this.currentRow.appendChild(item);

      return item;
   }

   /**
    * Добавляет месяцы в календарь
    */
   private putMonths(): void {
      for (let i = 0; i < 12; i++) {
         this.putMonth(i);
      }
   }

   /**
    * Добавляет элемент месяца в календарь
    *
    * @param monthNum - номер месяца
    */
   private putMonth(monthNum: number): void {
      const monthElement: HTMLElement = this.createMonth(monthNum, this.currentYear);
      monthElement.addEventListener('click', () => {
         this.level++;
         this.changeCurrentMonth(monthNum);
         this.putItems();
      });
   }

   /**
    * Создает элемент месяца
    *
    * @param month - номер месяца
    * @param year - отображаемый год
    * @return элемент месяца для календаря
    */
   private createMonth(month: number, year: number): HTMLElement {
      const monthElement: HTMLElement = this.createItem('month', 4);
      monthElement.textContent = this.getMonthString(month);
      monthElement.dataset.year = year.toString();
      monthElement.dataset.month = month.toString();

      // Если месяц совпадает с выбранной датой, отображаем как выбранный
      if (month === this.selectedMonth && parseInt(monthElement.dataset.year) === this.selectedYear) {
         monthElement.classList.add('selected');
      }

      return monthElement;
   }

   /**
    * Добавляет года в календарь
    */
   private putYears(): void {
      let yearShift = 5;

      for (let i = 0; i < 12; i++) {
         this.putYear(this.currentYear - yearShift--);
      }
   }

   /**
    * Добавляет элемент года в календарь
    *
    * @param year - номер года
    */
   private putYear(year: number): void {
      const yearElement: HTMLElement = this.createItem('year', 4);
      yearElement.textContent = year.toString();
      yearElement.addEventListener('click', () => {
         this.level++;
         this.changeCurrentYear(parseInt(yearElement.innerHTML));
      });

      // Если год сопадает с выбранной датой, отображаем как выбранный
      if (year === this.selectedYear) {
         yearElement.classList.add('selected');
      }
   }

   /**
    * Устанавливает выбранную дату
    *
    * @param selectedDate - выбранная дата
    */
   private setSelectedDate(selectedDate: Date): void {
      this.currentDate.setDate(selectedDate.getDate());
      this.selectedDay = this.currentDate.getDate();

      this.selectedMonth = this.currentMonth;
      this.selectedYear = selectedDate.getFullYear();
      this.currentMonth = this.selectedMonth;
      this.currentYear = this.selectedYear;

      // Записываем значение в родительское поле и скрытый инпут
      this.resultInput.value = this.getDateString();
      this.fieldLabel.textContent = this.resultInput.value;

      this.putItems();
   }

   /**
    * Получает форматированную строку с выбранной датой
    *
    * @return строка с датой
    */
   private getDateString(): string {
      const day: string = this.selectedDay < 10 ? `0${this.selectedDay}` : this.selectedDay.toString();

      const month: number = this.selectedMonth + 1;
      const monthString: string = month < 10 ? `0${month}` : month.toString();

      return `${day}.${monthString}.${this.selectedYear}`;
   }

   /**
    * Удаляет выбранную дату
    */
   private removeSelectedItem(): void {
      const selectedItem: HTMLElement | null = this.body.querySelector('.calendar__item.selected');
      if (selectedItem) {
         selectedItem.classList.remove('selected');
      }
   }

   /**
    * Меняет выбранный день
    */
   private changeSelectedDay(): void {
      const days: HTMLElement[] = Array.from(this.body.querySelectorAll('.calendar__item'));

      const selectedDay: HTMLElement | undefined = days.find(day => {
         return (
            parseInt(day.innerHTML) === this.selectedDay
            && parseInt(safeDataAttrGetter('month', day)) === this.selectedMonth
         );
      });

      if (selectedDay) {
         selectedDay.classList.add('selected');
      }
   }

   /**
    * Обрабатывает нажатие на заголовок с выбранной датой
    */
   private handleDateLabel(): void {
      this.selectedDateLabel.addEventListener('click', () => {
         if (this.level > 1) {
            this.level--;
            this.putItems();
         }
      });
   }

   /**
    * Обрабатывает нажатие на стрелки переключения страниц календаря
    */
   private handleArrows(): void {
      const arrowLeft: HTMLElement = mGetByID('calendarLeftArrow');
      const arrowRight: HTMLElement = mGetByID('calendarRightArrow');

      arrowLeft.addEventListener('click', () => this.arrowClickListener(-1));
      arrowRight.addEventListener('click', () => this.arrowClickListener(1));
   }

   /**
    * Обработчик нажатия на стрелку переключения страницы календаря
    *
    * @param offset  1 - следующая страница,
    *               -1 - предыдущая страница
    */
   private arrowClickListener(offset: number): void {
      switch (this.level) {
         case 3 :
            this.changeCurrentMonth(this.currentMonth + offset);
            break;
         case 2 :
            this.changeCurrentYear(this.currentYear + offset);
            break;
         case 1 :
            this.changeCurrentYear(this.currentYear + 10 * offset);
      }

      this.putItems();
   }

   /**
    * Очищает календарь и устанавливает родительское поле
    *
    * @param select родительское поле
    */
   public putFieldData(select: HTMLElement): Calendar {
      const field: HTMLElement = mClosest('[data-field]', select);
      const fieldLabel: HTMLElement = mQS('[data-field-label]', select);
      const resultInput: HTMLInputElement = mQS('[data-field-result]', field);

      this.select = select;
      this.field = field;

      this.setInterval();

      this.resultInput = resultInput;
      this.fieldLabel = fieldLabel;

      this.currentDate = this.resultInput.value ? getDateFromString(this.resultInput.value) : new Date();

      this.init();
      return this;
   }

   /**
    * Устанавливает доступный для выбора интервал
    */
   private setInterval(): void {

      switch (safeDataAttrGetter('interval', this.field)) {
         case DateInterval.Future:
            this.interval = DateInterval.Future;
            break;
         case DateInterval.Any:
            this.interval = DateInterval.Any;
            break;
         case DateInterval.Past:
            this.interval = DateInterval.Past;
            break;
         default:
            new LogicError('Не определен интервал даты для поля');
            this.interval = DateInterval.Any;
      }

   }

   /**
    * Меняет отображаемый месяц
    *
    * @param monthNum номер нового месяца
    */
   private changeCurrentMonth(monthNum: number): void {
      this.currentDate.setMonth(monthNum);
      this.currentDate.setDate(this.selectedDay);

      this.currentMonth = this.currentDate.getMonth();
      this.currentYear = this.currentDate.getFullYear();
   }

   /**
    * Меняет отображаемый год
    *
    * @param yearNum новый год
    */
   private changeCurrentYear(yearNum: number): void {
      this.currentDate.setFullYear(yearNum);
      this.currentYear = this.currentDate.getFullYear();

      this.putItems();
   }

   /**
    * Получает короткое название месяца по номеру
    *
    * @param monthNum номер месяца
    * @return короткое название месяца
    */
   private getMonthString(monthNum: number): string {
      const months: string[] = ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'];
      return months[monthNum];
   }

   /**
    * Получает полное название месяца по номеру отображаемого месяца
    *
    * @return Полное название месяца
    */
   private getFullMonthString(): string {
      const months: string[] = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль',
         'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
      return months[this.currentMonth];
   }

   /**
    * Отображает модальное окно календаря
    */
   public open(): void {
      this.modal.setAttribute('data-opened', 'true');
      this.overlay.setAttribute('data-opened', 'true');
   }


   /**
    * Устанавливает позицию модального окна календаря возле родительского поля
    */
   public setPosition(): Calendar {
      const coordinatesBox: DOMRect = this.select.getBoundingClientRect();
      const coordinatesWithOffset: Coordinates = getElementCoords(this.select);

      this.modal.style.top = coordinatesWithOffset.top - this.modal.offsetHeight + 'px';
      this.modal.style.left = coordinatesBox.left + 'px';

      return this;
   }

   /**
    * Закрывает модальное окно календаря
    */
   private close(): void {
      this.modal.setAttribute('data-opened', 'false');
      this.overlay.setAttribute('data-opened', 'false');
   }

   /**
    * Обрабатывает нажатие на фон календаря
    */
   private handleOverlay(): void {
      this.overlay = mGetByID('calendarOverlay');
      this.overlay.addEventListener('click', () => this.close());
   }

   /**
    * Определяет, что выбран доступный год
    *
    * @param year - год
    * @returns {boolean}
    */
   private isValidYear(year: number): boolean {
      return year > 1500 && year < 2200;
   }

}



