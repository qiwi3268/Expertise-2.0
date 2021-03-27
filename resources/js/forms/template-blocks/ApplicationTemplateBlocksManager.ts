import { TemplateBlocksManager } from './TemplateBlocksManager';
import { mQS, safeDataAttrGetter } from '../../lib/main';
import { TemplateBlock } from './blocks/TemplateBlock';
import { LogicError } from '../../lib/LogicError';
import { Cache, CacheSlots } from '../../lib/Cache';
import { FormJSON } from '../blocks/FormBlock';
import { SingleTemplateBlock } from './blocks/SingleTemplateBlock';
import { MultipleTemplateBlock } from './blocks/MultipleTemplateBlock';
import { MultipleTemplatePart } from './parts/MultipleTemplatePart';
import { DesignerPartForm } from './part-forms/DesignerPartForm';
import { FinancingSourcePartForm } from './part-forms/FinancingSourcePartForm';
import { DesignerTemplatePart } from './parts/DesignerTemplatePart';
import { RegisterExtractsPartForm } from './part-forms/RegisterExtractsPartForm';
import { ExecutorPartForm } from './part-forms/ExecutorPartForm';
import { ApplicationSingleTemplatePart } from './parts/ApplicationSingleTemplatePart';

/**
 * Типы шаблонных блоков
 */
enum TemplateBlockTypes {
   FinancingSources = 'financingSources',
   Applicant = 'applicant',
   AgreementCustomer = 'agreementCustomer',
   Developer = 'developer',
   TechnicalCustomer = 'technicalCustomer',
   Payer = 'payer',
   Designers = 'designers',
   SurveyDesigners = 'surveyDesigners',
   RegisterExtracts = 'registerExtracts'
}

/**
 * Представляет собой менеджер шаблонных блоков анкеты заявления
 */
export class ApplicationTemplateBlocksManager extends TemplateBlocksManager {

   private static instance: ApplicationTemplateBlocksManager;

   private constructor() {
      super();
      Cache.createSlot(CacheSlots.TemplateBlocks);
      // Cache.createSlot(CacheSlots.RegisterExtracts);
   }

   public static getInstance(): ApplicationTemplateBlocksManager {

      if (!ApplicationTemplateBlocksManager.instance) {
         ApplicationTemplateBlocksManager.instance = new ApplicationTemplateBlocksManager();
      }

      return ApplicationTemplateBlocksManager.instance;
   }

   /**
    * Создает шаблонный блок
    *
    * @param element - элемент шаблонного блока
    */
   public createTemplateBlock(element: HTMLElement): TemplateBlock {

      const type: string = safeDataAttrGetter('type', element);
      let templateBlock: TemplateBlock;

      switch (type) {
         case TemplateBlockTypes.FinancingSources:
            templateBlock = new MultipleTemplateBlock(element);
            templateBlock.setPartCreation(() => {
               new MultipleTemplatePart(templateBlock, new FinancingSourcePartForm())
            });
            Cache.slot(CacheSlots.TemplateBlocks).set(templateBlock.getType(), templateBlock);
            break;
         case TemplateBlockTypes.Applicant:
         case TemplateBlockTypes.AgreementCustomer:
         case TemplateBlockTypes.Developer:
         case TemplateBlockTypes.TechnicalCustomer:
         case TemplateBlockTypes.Payer:
            templateBlock = new SingleTemplateBlock(element);
            templateBlock.setPartCreation(() => new ApplicationSingleTemplatePart(templateBlock, new ExecutorPartForm()));
            Cache.slot(CacheSlots.TemplateBlocks).set(templateBlock.getType(), templateBlock);
            break;
         case TemplateBlockTypes.Designers:
         case TemplateBlockTypes.SurveyDesigners:
            templateBlock = new MultipleTemplateBlock(element);
            templateBlock.setPartCreation(() => new DesignerTemplatePart(templateBlock, new DesignerPartForm()));
            Cache.slot(CacheSlots.TemplateBlocks).set(templateBlock.getType(), templateBlock);
            break;
         case TemplateBlockTypes.RegisterExtracts:
            templateBlock = new MultipleTemplateBlock(element);
            templateBlock.setPartCreation(() => {
               console.log('create extract')
               new MultipleTemplatePart(templateBlock, new RegisterExtractsPartForm())
            });
            break;
         default:
            new LogicError('Ошибка при инициализации изменяемого блока: Не определен тип изменяемого блока');
      }

      return templateBlock!;
   }

   /**
    * Получает блок исполнителя с единственной частью по типу
    *
    * @param type - тип блока
    */
   public getSingleExecutorByType(type: string): TemplateBlock {
      return Cache.slot(CacheSlots.TemplateBlocks).get(type);
   }

   /**
    * Инициализирует и получает блок выписок СРО в области действия
    *
    * @param scope - область действия
    */
   public initExtractsTemplateBlock(scope: HTMLElement): MultipleTemplateBlock {
      const extractsBlock: HTMLElement = mQS(`[data-template-block][data-type="${TemplateBlockTypes.RegisterExtracts}"]`, scope);
      return this.createTemplateBlock(extractsBlock) as MultipleTemplateBlock;
   }

   /**
    * Получает форму шаблонных блоков в формате JSON
    */
   public getTemplateBlocksForm(): FormJSON {
      const form: FormJSON = {};

      const templateBlocks: Map<string, TemplateBlock> = Cache.slot(CacheSlots.TemplateBlocks).getValue();

      templateBlocks.forEach((templateBlock: TemplateBlock) => {
         form[templateBlock.getType()] = templateBlock.toJSON();
      });

      return form;
   }


}
