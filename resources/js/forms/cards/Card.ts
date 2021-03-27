import { getElementFullHeight, mQS } from '../../lib/main';

/**
 * Состояния раскрывающегося блока
 */
export enum CardStates {
   Opened = 'opened',
   Closed = 'closed'
}

/**
 * Представляет собой раскрывающийся блок
 */
export class Card {

   /**
    * Элемент раскрывающегося блока
    */
   protected readonly element: HTMLElement;

   /**
    * Тело раскрывающегося блока
    */
   protected readonly body: HTMLElement;

   /**
    * Иконка, указывающая состояние блока
    */
   protected readonly arrow: HTMLElement | null;

   /**
    * Элемент, при нажатии на который блок раскрывается
    */
   protected header: HTMLElement;

   /**
    * Переключает состояние блока
    */
   protected executeToggle: EventListenerOrEventListenerObject;

   /**
    * Текущее состояние блока
    */
   protected state: CardStates;

   public constructor(element: HTMLElement) {
      this.element = element
      this.body = mQS('[data-card-body]', this.element);
      this.arrow = this.element.querySelector('[data-card-arrow]');

      this.state = this.element.classList.contains(CardStates.Opened) ? CardStates.Opened : CardStates.Closed;

      this.executeToggle = () => this.toggle();

      this.handleHeader();
   }

   /**
    * Обрабатывает элемент, при нажатии на который блок раскрывается
    */
   protected handleHeader(): void {
      this.header = mQS('[data-card-header]', this.element);
      this.startListenToggle();
   }

   /**
    * Переключает состояние блока
    */
   protected toggle(): void {

      if (this.arrow) {
         this.arrow.classList.toggle('arrow-down');
         this.arrow.classList.toggle('arrow-up');
      }

      this.body.style.display === 'block' ? this.shrink() : this.expand();
   }

   /**
    * Закрывает блок
    */
   public shrink(): void {

      this.stopListenToggle();
      this.state = CardStates.Closed;
      this.body.style.maxHeight = getElementFullHeight(this.body).toString();
      setTimeout(() => this.body.style.maxHeight = '0', 1);

      setTimeout(() => {
         this.body.style.display = 'none';
         this.element.classList.add(CardStates.Closed);
         this.element.classList.remove(CardStates.Opened);
         this.startListenToggle();
      }, 300);
   }

   /**
    * Раскрывает блок
    */
   public expand(): void {

      this.stopListenToggle();
      this.state = CardStates.Opened;
      this.element.classList.add(CardStates.Opened);
      this.element.classList.remove(CardStates.Closed);
      this.body.style.display = 'block';
      this.body.style.maxHeight = '0';
      this.body.style.maxHeight = getElementFullHeight(this.body) + 'px';

      setTimeout(() => {
         this.body.style.maxHeight = '';
         this.startListenToggle();
      }, 300);
   }

   /**
    * Начинает слушать событие клика, по которому блок раскрывается
    */
   protected startListenToggle(): void {
      this.header.addEventListener('click', this.executeToggle);
   }

   /**
    * Перестает слушать событие клика, по которому блок раскрывается
    */
   protected stopListenToggle(): void {
      this.header.removeEventListener('click', this.executeToggle);
   }

   /**
    * Получает элемент раскрывающегося блока
    */
   public getElement(): HTMLElement {
      return this.element;
   }

}
