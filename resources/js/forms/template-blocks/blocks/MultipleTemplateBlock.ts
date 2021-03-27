import { TemplatePart } from '../parts/TemplatePart';
import { TemplateBlock } from './TemplateBlock';
import { Form, FormJSON } from '../../blocks/FormBlock';

/**
 * Представляет собой шаблонный блок с множеством частей
 */
export class MultipleTemplateBlock extends TemplateBlock {

   /**
    * Шаблонные части блока
    */
   protected parts: Set<TemplatePart> = new Set<TemplatePart>();

   public constructor(element: HTMLElement) {
      super(element);
   }

   /**
    * Сохраняет часть
    *
    * @param part - сохраняемая часть
    */
   public savePart(part: TemplatePart) {
      this.parts.add(part);
      this.complete();
   }

   /**
    * Удаляет часть
    *
    * @param part - удаляемая часть
    */
   public deletePart(part: TemplatePart) {
      this.setIsChanged(true);
      this.parts.delete(part);
      this.complete();
   }

   /**
    * Получает блок в виде формы в формате JSON
    */
   public toJSON(): FormJSON {
      const form: FormJSON = {};
      form['isChanged'] = this.isChanged();
      form['data'] = Array.from(this.parts).map(part => part.toJSON());
      return form;
   }

   /**
    * Определяет, является ли блок валидным
    */
   public isValid(): boolean {
      return Array.from(this.parts).some(part => part.isValid());
   }

   /**
    * Получает части блока
    */
   public getParts(): TemplatePart[] {
      return Array.from(this.parts.values());
   }

   /**
    * Определяет сохранен ли блок
    */
   public isSaved(): boolean {
      return Array.from(this.parts).some(part => !!part && part.isSaved());
   }

}
