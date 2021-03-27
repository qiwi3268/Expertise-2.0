import { Card, CardStates } from './Card';
import { addDataAttrMutationHandler, mQS, safeDataAttrGetter } from '../../lib/main';

/**
 * Представляет собой раскрывающийся блок анкеты заявления
 */
export class ApplicationFormCard extends Card {

   /**
    * Связанная метка блока в сайдбаре
    */
   protected sidebarLabel: HTMLElement;

   /**
    * Наименование блока
    */
   protected name: string;

   public constructor(element: HTMLElement) {
      super(element);

      this.name = safeDataAttrGetter('name', this.element);

      this.handleSidebarLabel();
   }

   /**
    * Обрабатывает связанную метку блока в сайдбаре
    */
   protected handleSidebarLabel(): void {
      this.sidebarLabel = mQS(`[data-form-label][data-name=${this.name}]`, document.documentElement);

      if (this.element.hasAttribute('data-displayed')) {
         this.sidebarLabel.dataset.displayed = safeDataAttrGetter('displayed', this.element);
      }

      this.sidebarLabel.addEventListener('click', () => {
         this.setSelectedSidebarLabel();
         this.element.scrollIntoView({block: 'start', behavior: 'smooth'});

         if (this.state === CardStates.Closed) {
            this.expand();
         }
      });

      this.observeParentForm();
   }

   /**
    * Устанавливает выбранную метку в сайдбаре
    */
   protected setSelectedSidebarLabel(): void {
      const selectedLabel: HTMLElement | null = document.querySelector('[data-form-label][data-selected="true"]');
      if (selectedLabel) {
         selectedLabel.removeAttribute('data-selected');
      }
      this.sidebarLabel.dataset.selected = 'true';
   }

   /**
    * Меняет состояние блока и метки в сайдбаре
    * в зависимости от состояния формы в блоке
    */
   protected observeParentForm(): void {
      let parentForm: HTMLElement = this.element;

      if (
         this.element.hasAttribute('data-template-card')
         && !this.element.hasAttribute('data-template-block')
      ) {
         parentForm = mQS('[data-template-block]', this.element);
      }

      addDataAttrMutationHandler(parentForm, 'displayed', (attrValue: string) => {
         this.sidebarLabel.dataset.displayed = attrValue;
      });

      addDataAttrMutationHandler(parentForm, 'state', (attrValue: string) => {
         this.sidebarLabel.dataset.state = attrValue;
      });
   }


}



