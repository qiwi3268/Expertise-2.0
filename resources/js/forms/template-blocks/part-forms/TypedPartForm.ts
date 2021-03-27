import { mQSA, safeDataAttrGetter } from '../../../lib/main';
import { Dependencies, DependenciesHandler } from '../../../dependencies/DependenciesManager';
import { TemplatePart } from '../parts/TemplatePart';
import { Cache, CacheSlots } from '../../../lib/Cache';
import { PartForm } from './PartForm';
import { Form, FormBlock } from '../../blocks/FormBlock';
import { CompositeFormBlock } from '../../blocks/CompositeFormBlock';

/**
 * Представляет собой форму части шаблонного блока, разбитую по типам
 */
export abstract class TypedPartForm<T extends TemplatePart = TemplatePart> extends PartForm {

   /**
    * Инициализирует форму
    *
    * @param parentPart - родительская часть
    */
   public init(parentPart: T): void {
      super.init(parentPart);

      Cache.slot(CacheSlots.FieldDependencies)
         .get<DependenciesHandler>(Dependencies.SingledDisplay)
         .initMainField(this.mainField.getElement(), this.partElement);

      this.initParts();
   }

   /**
    * Инициализирует элементы формы
    */
   protected initFormElements(): void {
      this.form = new CompositeFormBlock(this.partElement);
      this.form.initInnerFields();

      const mainFieldName: string = safeDataAttrGetter('partMainField', this.partElement);
      this.mainField = this.form.getFieldByName(mainFieldName);
   }

   /**
    * Инициализирует части формы
    */
   protected initParts(): void {
      const formParts: HTMLElement[] = mQSA('[data-form-part]', this.partElement);
      formParts.forEach((formPartElement: HTMLElement) => {
         const formPart: Form = new FormBlock(formPartElement);
         formPart.initInnerFields();

         this.form.addForm(safeDataAttrGetter('formPartKey', formPartElement), formPart);

         this.getDependenciesManagers().forEach((manager: DependenciesHandler) => {
            manager.handleNewMainFieldsParentElement(formPartElement);
         });
      });
   }

   /**
    * Получает менеджеры зависимостей, относящиеся к форме
    */
   public abstract getDependenciesManagers(): DependenciesHandler[];


}
