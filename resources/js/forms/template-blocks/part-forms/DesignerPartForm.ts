import { mQS } from '../../../lib/main';
import { FormJSON } from '../../blocks/FormBlock';
import { TemplateBlock } from '../blocks/TemplateBlock';
import { ApplicationTemplateBlocksManager } from '../ApplicationTemplateBlocksManager';
import { DesignerTemplatePart } from '../parts/DesignerTemplatePart';
import { Dependencies, DependenciesHandler } from '../../../dependencies/DependenciesManager';
import { Cache, CacheSlots } from '../../../lib/Cache';
import { CompositeFormBlock } from '../../blocks/CompositeFormBlock';
import { ExecutorPartForm } from './ExecutorPartForm';

/**
 * Представляет собой форму части шаблонного блока проектировщиков из анкеты заявления
 */
export class DesignerPartForm extends ExecutorPartForm {

   /**
    * Родительская части
    */
   protected parentPart: DesignerTemplatePart;

   /**
    * Инициализирует форму
    *
    * @param parentPart - родительская часть
    */
   public init(parentPart: DesignerTemplatePart): void {
      this.parentPart = parentPart;
      this.partElement = this.parentPart.getElement();

      this.form = new CompositeFormBlock(this.partElement);
      this.form.initInnerFields();

      this.initFormElements();

      const dependenciesManager: DependenciesHandler = Cache.slot(CacheSlots.FieldDependencies)
         .get<DependenciesHandler>(Dependencies.SingledDisplay);

      dependenciesManager.initMainField(this.mainField.getElement(), this.partElement);

      const isRequiredExtract: HTMLElement = mQS('[data-field][data-name="isRequiredExtract"]', this.partElement);
      dependenciesManager.initMainField(isRequiredExtract, this.partElement);

      this.initParts();

   }

   /**
    * Инициализирует части формы
    */
   protected initParts(): void {
      this.initExtracts();
      super.initParts();
   }

   /**
    * Инициализирует внутренний шаблонный блок выписок СРО
    */
   protected initExtracts(): void {

      const templateBlock: TemplateBlock = this.parentPart.getParentBlock();
      const extracts: HTMLElement = templateBlock.createBlock(this.parentPart.getBody(), '[data-display-block][data-name="registerExtracts"]');

      this.parentPart.setExtracts(ApplicationTemplateBlocksManager.getInstance().initExtractsTemplateBlock(extracts));
   }

   /**
    * Получает форму в формате JSON
    */
   public toJSON(): FormJSON {
      const fullForm: FormJSON = this.form.toJSON();
      fullForm[this.parentPart.getExtracts().getType()] = this.parentPart.getExtracts().toJSON();
      return fullForm;
   }

}
