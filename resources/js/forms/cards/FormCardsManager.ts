import { Card } from './Card';
import { mClosest, safeWeakMapGetter, safeWeakMapSetter } from '../../lib/main';

/**
 * Менеджер для работы с раскрывающимися блоками
 */
export abstract class FormCardsManager {

   /**
    * Раскрывающиеся блоки на странице
    */
   protected cards: WeakMap<HTMLElement, Card> = new WeakMap<HTMLElement, Card>();

   /**
    * Инициализирует раскрывающиеся блоки на странице
    */
   public initPageCards(): void {
      const cards: NodeListOf<HTMLElement> = document.querySelectorAll('[data-card]');
      cards.forEach((card: HTMLElement) => this.initCard(card));
   }

   /**
    * Инициализирует раскрывающийся блок
    *
    * @param card - элемент раскрывающегося блока
    */
   protected initCard(card: HTMLElement): void {
      const formCard: Card = new Card(card);
      safeWeakMapSetter(this.cards, formCard.getElement(), formCard);
   }

   /**
    * Закрывает раскрывающийся блок, к которому относится элемент
    *
    * @param innerElement - внутренний элемент
    */
   public shrinkParentCard(innerElement: HTMLElement): void {

      const cardElement: HTMLElement = mClosest('[data-card]', innerElement);
      const card: Card = safeWeakMapGetter(this.cards, cardElement);
      card.shrink();
   }

}



