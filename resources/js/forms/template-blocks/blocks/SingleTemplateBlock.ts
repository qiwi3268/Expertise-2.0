import { mClosest, mQS } from '../../../lib/main';
import { TemplatePart } from '../parts/TemplatePart';
import { TemplateBlock } from './TemplateBlock';
import { Form, FormJSON } from '../../blocks/FormBlock';

/**
 * Представляет собой шаблонный блок с единственной частью
 */
export class SingleTemplateBlock extends TemplateBlock {

   /**
    * Шаблонная часть
    */
   protected part: TemplatePart;

   protected card: HTMLElement;

   public constructor(element: HTMLElement) {
      super(element);
      this.card = mClosest('[data-template-card]', this.element);
   }

   /**
    * Создает часть
    */
   protected createPart(): void {
      this.partCreation();
      this.hideAddPartButton();
   }

   /**
    * Скрывает кнопку создания части
    */
   protected hideAddPartButton(): void {
      this.addPartButton.dataset.displayed = 'false';
   }

   /**
    * Отображает кнопку создания части
    */
   public showAddPartButton(): void {
      this.addPartButton.dataset.displayed = 'true';
   }

   /**
    * Сохраняет часть
    */
   public savePart(part: TemplatePart) {
      this.part = part;
      this.complete();
   }

   /**
    * Определяет сохранен ли блок
    */
   public isSaved(): boolean {
      return !!this.part && this.part.isSaved();
   }

   /**
    * Получает блок в виде формы в формате JSON
    */
   public toJSON(): FormJSON {
      const form: FormJSON = {};
      form['isChanged'] = this.isChanged();
      form['data'] = this.part ? this.part.toJSON() : {};
      return form;
   }

   /**
    * Определяет, является ли блок валидным
    */
   public isValid(): boolean {
      return !!this.part && this.part.isValid();
   }


}
