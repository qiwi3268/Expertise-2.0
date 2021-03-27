import { FormCardsManager } from './FormCardsManager';
import { Card } from './Card';
import { ApplicationFormCard } from './ApplicationFormCard';
import { safeWeakMapSetter } from '../../lib/main';

/**
 * Менеджер для работы с раскрывающимися блоками анкеты заявления
 */
export class ApplicationFormCardsManager extends FormCardsManager {

   protected static instance: ApplicationFormCardsManager;

   public static getInstance(): FormCardsManager {
      if (!ApplicationFormCardsManager.instance) {
         ApplicationFormCardsManager.instance = new ApplicationFormCardsManager();
      }

      return ApplicationFormCardsManager.instance;
   }

   /**
    * Инициализирует раскрывающийся блок
    *
    * @param card - элемент раскрывающегося блока
    */
   protected initCard(card: HTMLElement): void {

      let formCard: Card;
      if (card.hasAttribute('data-form') || card.hasAttribute('data-template-card')) {
         formCard = new ApplicationFormCard(card);
      } else {
         formCard = new Card(card);
      }

      safeWeakMapSetter(this.cards, formCard.getElement(), formCard);
   }

}
